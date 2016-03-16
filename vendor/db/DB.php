<?php
class DB{
	// config/db.php
	public static $db_type;
	public static $pdo_fetch;
	public static $table_prefix;
	public static $relevances;

	//PDO Object
	public static $db;
	//连接数据库
	public static function Connection(){
		if (static::$db === null) {
			static::$db = (new DBConnection)->getDb();
		}
	}
	//返回PDO对象
	public static function getDb(){
		static::Connection();
		return static::$db;
	}

	public static function fullTableName($tableName){
		return static::$table_prefix . $tableName;
	}

	public static function relateTable($table_0, $table_1){
		if (isset(DB::$relevances[$table_0 . '&' . $table_1])) {
			$_table = $table_0;
			$__table = $table_1;
		}elseif (isset(DB::$relevances[$table_1 . '&' . $table_0])) {
			$_table = $table_1;
			$__table = $table_0;
		}else
			throw new DBException('There is no relevance between table ' . $table_0 . ' and ' . $table_1, 1);
		$value = DB::$relevances[$_table . '&' . $__table];
		return DB::$table_prefix . $_table . '.' . $value[0] . '=' . DB::$table_prefix . $__table . '.' . $value[1];
	}
	
	public function PDOExecInfo($result){
		if ($result !== false) 
			return $result;
		elseif(DEBUG === true){
			var_dump(DB::getDb()->errorInfo());
			exit();
		}else
			return false;
	}

	// public static function selectPrefix($tableName, $select){
	// 	$fullTableName = DB::$table_prefix . $tableName;
	// 	$values = explode(',', $select);
	// 	foreach ($values as $key => $value) {
	// 		$values[$key] = $fullTableName . $value;
	// 	}
	// 	return $values;
	// }

	// /**
	//  * Model::add('user','name,password',['itchin',123456],['orthocore','abcdefg']);
	//  */
	// public static function add($tableName, $field, array $values){
	// 	$db = static::getDb();
	// 	$fullTableName = static::fullTableName($tableName);
	// 	$args = func_get_args();
	// 	$len = count($args);
	// 	if ($len < 3) 
	// 		throw new DBException('Must have at least three parameters ', 1);

	// 	$fields = explode(',', $field);
	// 	$str = '`'.array_shift($fields).'`';
	// 	foreach ($fields as $value) {
	// 		$str .= ',`'.$value.'`';
	// 	}

	// 	$n = count($fields);//插入下标数量
	// 	unset($args[0],$args[1]);
	// 	$valuesStr = '';
	// 	foreach ($args as $value) {
	// 		$valuesStr .= '("'.implode('","', $value).'"),';
	// 	}
		
	// 	$sql = 'INSERT INTO `'.$fullTableName.'`('.$str.') VALUES '.rtrim($valuesStr,',');
	// 	return $db->exec($sql);
	// }

	// public static function del($tableName, $condition = []){
	// 	$db = static::getDb();
	// 	$sql = 'DELETE FROM '.static::fullTableName($tableName);
	// 	if ($condition != null) {
	// 		$sql .= ' WHERE '.static::condition($condition);
	// 	}
	// 	return $db->exec($sql);
	// }

	// public static function update($tableName, $fields, $condition = []){
	// 	$db = static::getDb();
	// 	$fieldsString = '';
	// 	foreach ($fields as $key => $value) {
	// 		if (is_string($value) || is_numeric($value))
	// 			$fieldsString .= "`$key`='$value',";
	// 		else
	// 			$fieldsString .= "`$key`=`$key`$value[0]$value[1]";
	// 	}
	// 	$fieldsString = rtrim($fieldsString, ',');
	// 	$sql = 'UPDATE '.static::fullTableName($tableName).' SET '.$fieldsString;
	// 	if ($condition != null) {
	// 		$sql .= ' WHERE '.static::condition($condition);
	// 	}
	// 	return $db->exec($sql);
	// }

	// public static function condition(array $condition){
	// 	$where = [];
	// 	foreach ($condition as $key => $value) {
	// 		if (is_string($value) || is_numeric($value)) {
	// 			array_push($where, "`$key` = '$value'");
	// 		}elseif (is_array($value)) {
	// 			if (in_array($value[0], ['>','>=', '<', '<=', '=']) && is_numeric($value[1])) {
	// 				array_push($where, "`$key`".$value[0].$value[1]);
	// 			}else{
	// 				array_push($where, "`$key`".' in ("'.implode('","', $value).'")');
	// 			}
	// 		}
	// 	}
	// 	return implode(',', $where);
	// }
}
