<?php
class View{
	public $ctrlName;
	private $UrlBuild;

	public $js;

	public function __construct($ctrlName){
		$this->ctrlName = $ctrlName;
	}

	/**
	 * @param string 要读取的view文件的名称
	 * @param array 传入buffered中的参数 
	 * @return string 返回view页面
	 */
	public function renderPhpFile($_file_, $_params_ = []){
		ob_start();
		ob_implicit_flush(false);
		extract($_params_, EXTR_OVERWRITE);
		require(APP_PATH . '/view/' . $this->ctrlName  . '/' . $_file_ . '.php');
		if (!isset($js)) {
			$js = null;
		}
		$this->js = $js;
		// return ob_get_clean();
	}

	public function getJs(){
		return $this->js;
	}

	public function getView(){
		return ob_get_clean();
	}

	/**
	 * 生成URL路径
	 * @param string 控制器/动作 如果没'/'，则为本控制器内的方法
	 * @param array url中的get参数
	 */
	public function urlTo($action, $params = []){
		if (strpos($action, '/') === false) {
			$action = $this->ctrlName . '/' . $action;
		}

		if (empty($this->UrlBuild)) {
			$this->UrlBuild = new UrlBuild;
		}
		return $this->UrlBuild->urlTo($action, $params);
	}
}
