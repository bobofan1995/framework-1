<?php
include(VENDOR_PATH . '/base/Application.php');
include(VENDOR_PATH . '/base/AutoLoad.php');
spl_autoload_register(['AutoLoad', 'autoLoader']);

class YC{
	public static $app;
	public static $baseUrl;//站点基本网址路径

	public static function run(&$config){
		static::$baseUrl = substr($_SERVER['SCRIPT_NAME'], 0, -10);
		$Application = new Application($config);
	}
}
