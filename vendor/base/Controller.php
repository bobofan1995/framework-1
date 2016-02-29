<?php
namespace yc\base;

class Controller{
	public $layout;//布局文件的名称，在view/_layout目录下
	public $title = null;//html页面的标题
	public $js;//从view中返回的javascript

	/**
	 * 如果只有一个头元素，可以只赋值为一维数组
	 * 如果有多个，赋值为二维数组
	 * eg:
	 * ['meta','name' => 'namevalue', 'content' => 'content']
	 * or:
	 * [
	 *		['meta','name' => 'namevalue', 'content' => 'content'],
	 *		['link','href' => '#', 'type' => 'texy'],
	 * ]
	 */
	public $globalHead = [];//全局head标签,以数组为初始化
	//个别页面追加的head标签
	public $head = null;
	//所访问的控制器全名
	public $ctrlName;

	public function __construct($ctrlName, $layout){
		$this->ctrlName = $ctrlName;
		$this->layout = $layout;
	}

	/**
	 * 中间件注册
	 * 所有中间件在Middleware.php中实现
	 * 数组的key为中间件类中的方法，其value为控制器内要执行此方法的action，以数组形式存储
	 */
	public function registerMiddleware(){
		return [
			
		];
	}

	public function _run($action){
		/**
		 * 执行中间件
		 */
		$registerMiddleware = $this->registerMiddleware();
		if (!empty($registerMiddleware))
			$middleware = new \Middleware;	
		foreach ($registerMiddleware as $key => $actionString) {
			$actionList = explode(',', $actionString);
			if (in_array('*', $actionList) || in_array(ucfirst($action), $actionList) || in_array(lcfirst($action), $actionList)) {
				if ($middleware->$key() === false)
					return false;
			}
		}

		$methodOfAction = 'action' . ucfirst(strtolower($action));
		$Rft = new \ReflectionMethod($this, $methodOfAction);
		$params = [];
		//通过循环来控制参数的传入
		foreach ($Rft->getParameters() as $object) {
			if (isset($_GET[$object->name])) 
				array_push($params, $_GET[$object->name]);
		}
		return call_user_func_array([$this, $methodOfAction], $params);
	}

	public final function view($file, $params = []){
		$View = new \View($this->ctrlName);
		$View->renderPhpFile($file, $params);
		$this->js = $View->getJs();
		return $View->getView();
	}

	private final function _registHtmlHead($header){
		if (empty($header)) {
			return null;
		}

		$head = '';
		foreach ($header as $array) {
			$temp = '';
			$element = array_shift($array);
			foreach ($array as $key => $value) 
				$temp .= ' ' . $key . '="' . $value . '"';
			
			$head .= "\t<" . $element . $temp . "/>\n";
		}
		return $head;
	}

	public final function _getTitle(){
		return $this->title;
	}

	public final function _getLayout(){
		return $this->layout;
	}

	public final function _getHead(){
		if (empty($this->head)) {
			return $this->_registHtmlHead(
				$this->_arrayUpgrade($this->globalHead)
			);
		}
		return $this->_registHtmlHead(
			array_merge(
				$this->_arrayUpgrade($this->globalHead), 
				$this->_arrayUpgrade($this->head)
			)
		);
	}

	public final function _getJs(){
		return $this->js;
	}

	/**
	  * 一维数组升级为二维数组
	  * $this->global 和 $this->head 用
	  */ 
	private final function _arrayUpgrade(array $array){
		if ($array == null) {
			return [];
		}
		if (is_array(current($array))) {
			return $array;
		}
		return [$array];
	}

}
