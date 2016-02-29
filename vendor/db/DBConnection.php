<?php
class DBConnection {
	public $host;
	public $dbname;
	public $user;
	public $password;
	public $charset;

	public $pdo;

	public function __construct(){
		$this->initParam();	
		$this->initSQL();
	}

	private function initParam(){
		$db_config = require(APP_PATH . '/config/db.php');
		$this->host = $db_config['host'];
		$this->port = $db_config['port'];
		$this->dbname = $db_config['dbname'];
		$this->user = $db_config['user'];
		$this->password = $db_config['password'];
		$this->charset = $db_config['charset'];

		DB::$db_type = $db_config['type'];
		DB::$pdo_fetch = $db_config['pdo_fetch'];
		DB::$table_prefix = $db_config['table_prefix'];
		DB::$relevances = $db_config['relevances'];
		Model::$submeter = $db_config['submeter'];
		Model::$delimiter = $db_config['delimiter'];
	}

	public function initSQL(){
		switch (DB::$db_type) {
			case 'mysql':
				break;
			
			default:
				throw new Exception('nonsupport SQL connected type: ' . DB::$db_type);
				break;
		}
		$method = DB::$db_type . '_connect';
		$this->$method();
	}

	public function mysql_connect(){
		try {
			$dsn = 'mysql:host=' . $this->host . ';port='. $this->port . ';dbname=' . $this->dbname;
			$this->pdo = new PDO($dsn, $this->user, $this->password);

			if ($this->charset != null) {
				$this->pdo->exec("set names '$this->charset'");
			}
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}

	public function getDb(){
		return $this->pdo;
	}
}
