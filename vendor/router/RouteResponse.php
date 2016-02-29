<?php
/**
 * 处理访问应用的路由请求
 */
class RouteResponse{
	public static $enableRewrite;
	public static $suffix;
	public $defCtrl;
	public $defAct;

	public $actionArray;

	public function __construct(&$routeConf){
		$this->defCtrl = $routeConf['controller'];
		$this->defAct = $routeConf['action'];
		//即使开启路由功能，但如果path为空且存在$_GET['yc']变量，则以该变量为路由参数
		if(static::$enableRewrite === true){
			static::$suffix = $routeConf['suffix'];
			$path = (new UrlRule($routeConf))->matchAllRules();

			if($path == null && isset($_GET['yc']))
				$path = $_GET['yc'];
		}else{
			$path = isset($_GET['yc']) ? $_GET['yc'] : null;
		}
		// echo $path;var_dump($_GET); exit();
		$this->actionArray = $this->pathInit($path);
		// $this->run($params);
	}

	/**
	 * 补全访问路径
	 * @param array
	 * @return array
	 */
	public function pathInit($path = ''){
		$params = explode('/', trim($path,'/'));
		if ($params[0] == null) {
			$params[0] = $this->defCtrl;
		}
		if (empty($params[1])) {
			$params[1] = $this->defAct;
		}
		return $params;
	}


	public function getActionArray(){
		return $this->actionArray;
	}


	/**
	 * 检查是否存在对应的控制器或方法
	 * 若定义ControllerNotFoundException时调用
	 */
	// public function ctrl_exists($ctrl){
	// 	$ctrlName = ucfirst(strtolower($ctrl)) . 'Controller';
	// 	if (file_exists($this->ctrlPath . '/' . $ctrlName)) {
	// 		return $ctrlName;
	// 	}
	// 	return false;
	// }

	// public function action_exists($controller, $action){
	// 	$actName = 'action' . ucfirst(strtolower($action));
	// 	$rs = in_array(get_class_methods($controller), $action);
	// 	if ($rs !== false) {
	// 		return $actName;
	// 	}
	// 	return false;
	// }
}
