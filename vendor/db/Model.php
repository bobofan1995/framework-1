<?php
class Model {
	/**
	 * 查询该表的id,name字段，及new表的title,contend字段
	 * 并使该表与new表关联
	 * find('id,name',
	 *	['id'=>1,'age'=>['>=',9]])
	 * ->with('news','title,content',
	 *	[
	 *		'id'=>[1,11,111],//new.id为1,11,111
	 *		'sex'=>'男',//性别为男
	 *		'age'=>['>',10] //年龄大于10
	 *	]
	 * )
	 */
	public $fullTableName;
	public $tableName;

	/**
	 * sql中进行操作的表格，find() & with()
	 */
	public $tables = [];

	/**
	 * 初始化为数组,在find()和with()中的select操作，
	 * 将select项加入该数组
	 * 最终在exec()中将数组转换为字符串生成sql
	 */	
	public $select = [];

	public $where;//sting
	public $param;//array PDO::execute绑定参数
	public $with = [];//array 表关联,需初始化为空数组
	//for sql limit
	public $start = 0;//初始化为0
	public $quantity;//int
	public $order;

	//分表的数量
	public static $submeter;
	//分表时的分界符
	public static $delimiter;


	public function __construct($id = null){
		DB::Connection();
		$this->setTableNames($id);
	}

	/**
	 * 查询操作完成后，重置成员变量
	 */
	private final function _paramInit(){
		$this->tables = $this->select = $this->with = [];
		$this->where = $this->param = $this->quantity = $this->order = null;
		$this->start = 0;
	}

	public function setTableNames($id){
		$this->tableName = static::className();
		$this->fullTableName = DB::fullTableName($this->tableName);
		if ($id !== null) {
			$this->fullTableName .= static::$delimiter . $id % static::$submeter;
		}
	}

	/**
	 * 返回当前Model的实例化对象
	 */
	public static function model($id = null){
		$className = get_called_class();
		return new $className($id);
	}

	public static function className(){
		return lcfirst(get_called_class());
	}

	public function findOne($select = '', $where = []){
		return $this->find($select, $where)->limit(1);
		
	}

	public function find($select = '', $where = []){
		array_push($this->tables, $this->fullTableName);
		$this->select = DBCommand::select($this->fullTableName, $select);
		list($this->where, $this->param) = DBCommand::where($this->fullTableName, $where);
		return $this;
	}

	public function order($order){
		$this->order = $order;
		return $this;
	}

	public function limit(){
		$args = func_get_args();
		$count = count($args);
		if ($count === 1) {
			$this->quantity = $args[0];
		}elseif ($count === 2) {
			$this->start = $args[0];
			$this->quantity = $args[1];
		}else
			throw new DBException("Only 2 parameters at most", 1);
		
		return $this;
	}
	
	/** 
	 * 用于表关联
	 * @param relevance string|array 如果是字符串，则表示当前model关联该表，
	 * @param select string|array
	 * 
	 * 如果是数组，则为该数组中key与value两表相互关联。表与表的关联定义在config/db.php中。
	 * 
	 */
	public final function with($relevance, $select = '', $where = [], $where_ = []){
		if (is_string($relevance) && is_string($select)) {
			$this->_withOneTable($relevance, $select, $where);
		}elseif (is_array($relevance) && is_array($select)) {
			$this->_withTwoTable($relevance, $select, $where, $where_);
		}
		else
			throw new DBException('Parameter "relevance" and "select" must be string or double demensional array', 1);
		return $this;
	}
	/**
	 * @param relevance string 关联的表名
	 * @param select string
	 */
	private final function _withOneTable($relevance, $select, array $where){
		$fullTableName = DB::fullTableName($relevance);
		array_push($this->tables, $fullTableName);
		array_push($this->with, DB::relateTable($this->tableName, $relevance));
		$this->select = array_merge($this->select, DBCommand::select($fullTableName, $select));

		$values = DBCommand::where($fullTableName, $where);
		if ($values[0] != null) 
			$this->where .= ' and ' . $values[0];
		if ($values[1] != null)
			array_push($this->param, $values[1]);
	}

	/**
	 * @param relevance array
	 * @param select array
	 */
	private final function _withTwoTable(array $relevances,array $selects,array $where,array $where_){
		$table_0 = DB::fullTableName($relevances[0]);
		$table_1 = DB::fullTableName($relevances[1]);
		array_push($this->tables, $table_0, $table_1);
		array_push($this->with, DB::relateTable($relevances[0], $relevances[1]));
		$this->select = array_merge($this->select, DBCommand::select($table_0, $selects[0]), DbCommand::select($table_1, $selects[1]));

		$value_ = DBCommand::where($table_0, $where);
		$value__ = DBCommand::where($table_1, $where_);
		if ($value_[0] != null)
			$this->where .= ' and ' . $value_[0];
		if ($value__[0] != null)
			$this->where .= ' and ' .$value__[0];
		if ($value_[1] != null)
			$this->param = array_merge($this->param, $value_[1]);
		if ($value__[1] != null)
			$this->param = array_merge($this->param, $value__[1]);
	}

	/**
	 * 根据成员变量中保存的查询条件，生成sql语句
	 */
	public function createSql(){
		$sql = 'SELECT '. implode(',', $this->select) .' FROM ' . implode(',', $this->tables);
		if ($this->where != null)
			array_unshift($this->with, $this->where);
		if ($this->with != null) 
			$sql .= ' WHERE ' . implode(' and ', $this->with);
		if ($this->order != null)
			$sql .= " ORDER BY $this->order";
		if ($this->quantity != null) 
			$sql .= " LIMIT $this->start,$this->quantity";
		return $sql;
	}

	/**
	 * 返回PDO预处理对象中的待绑定参数变量
	 */
	public function getParam(){
		return $this->param;
	}
	
	//换行查询操作
	public function exec(){
		//预处理
		$prepareStatement = DB::getDb()->prepare($this->createSql());
		//绑定SQL预处理参数
		$prepareStatement->execute($this->param);
		if ($this->quantity === 1) {
			$this->_paramInit();
			return $prepareStatement->fetch(DB::$pdo_fetch);
		}else{
			$this->_paramInit();
			return $prepareStatement->fetchAll(DB::$pdo_fetch);
		}
		
	}

	/**
	 * User::add('name,password',['itchin',123456],['orthocore','abcdefg']);
	 */
	public static function add($field, array $values){
		$fields = explode(',', $field);
		$str = '`'.implode('`,`', $fields).'`';
		$n = count($fields);//插入下标数量
		
		$valuesStr = null;
		$valuesStr .= '("'.implode('","', $values).'"),';
		
		$sql = 'INSERT INTO `'.DB::fullTableName(static::className()).'`('.$str.') VALUES '.rtrim($valuesStr,',');
		return DB::getDb()->exec($sql);
	}

	public static function del($condition = []){
		$sql = 'DELETE FROM '.DB::fullTableName(static::className());
		if ($condition != null) {
			$sql .= ' WHERE '.DBCommand::condition($condition);
		}
		return DB::getDb()->exec($sql);
	}

	public static function update($fields, $condition = []){
		$fieldsString = '';
		foreach ($fields as $key => $value) {
			if (is_string($value) || is_numeric($value))
				$fieldsString .= "`$key`='$value',";
			else
				$fieldsString .= "`$key`=`$key`$value[0]$value[1]";
		}
		$fieldsString = rtrim($fieldsString, ',');
		$sql = 'UPDATE `'.DB::fullTableName(static::className()).'` SET '.$fieldsString;
		if ($condition != null) {
			$sql .= ' WHERE '.DBCommand::condition($condition);
		}
		return DB::getDb()->exec($sql);
	}


}
