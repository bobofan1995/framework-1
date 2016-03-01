<?php
class Application{
	/**
	 * 保存最终生成的配置信息
	 */
	private $config;


	public function Application(&$config){
		$this->reginstErrorHandle();
		
		if (!empty($config['html_charset'])) {
			header('Content-type:text/html;charset='.$config['html_charset']);
			unset($config['html_charset']);
		}

		$this->Configure($config);
		unset($config);
		$this->setStaticConfig();

		YC::$app = new ComponentLoader;
		$actionArray = (new RouteResponse($this->config['route']))->getActionArray();
		$this->run($actionArray);

		//关闭数据库连接
		if(isset(DB::$db))
			DB::$db = null;
	}
	/**
	 * 为Exception类指定操作句柄
	 */
	protected function reginstErrorHandle(){
		set_exception_handler(function($Exception){
			if (method_exists($Exception, 'errorMessage')) 
				echo $Exception->errorMessage();
			else
				echo $Exception->getMessage();
		});
	}

	/**
	 * 把用户自定义的配置文件跟程序默认配置文件全并
	 */
	public function Configure(&$config){
		$defaultConfig = include(VENDOR_PATH . '/data.php');
		foreach ($config as $key => $cfg) {

			if (is_array($cfg)) {
				if (isset($defaultConfig[$key])) 
					switch ($key) {
						case 'classMap':
						// case 'classFolder':
						/**
						 * 以defaultConfig.php中的项为优先
						 * 以classMap为例，若在用户自定义的config.php中，
						 * 同时出现Controller类的所在路径指向，采用defaultConfig.php中的定义路径,config.php中的无效
						 */
							$defaultConfig[$key] = array_merge($cfg, $defaultConfig[$key]);
							break;
						/**
						 * 其它项以config.php为优先
						 * 如：在defaultConfig.php中指定controller为index
						 * 而在config.php可重指定为home
						 */
						default:
							$defaultConfig[$key] = array_merge($defaultConfig[$key], $cfg);
							break;
					}
					
				else
					$defaultConfig[$key] = $cfg;
			}else
				$defaultConfig[$key] = $cfg;
				
		}
		$this->config = $defaultConfig;
		unset($config, $defaultConfig);
	}

	//对静态变量的配置进行赋值
	public function setStaticConfig(){
		AutoLoad::$classMap = $this->config['classMap'];
		AutoLoad::$classFolder = $this->config['classFolder'];
		RouteResponse::$enableRewrite = $this->config['route']['enableRewrite'];

		unset($this->config['classMap'], $this->config['classFolder'], $this->config['enableRewrite']);
	}

	/**
	 * 启动控制器
	 * @param array
	 * 
	 */
	public function run(array $actionArray){
		$controller = ucfirst(strtolower($actionArray[0])) . 'Controller';
		require(APP_PATH . '/controller/' . $controller . '.php');
		$Controller = new $controller($actionArray[0], $this->config['layout']);
		$view = $Controller->_run($actionArray[1]);
		$js = $Controller->_getJs();
		$layout = $Controller->_getLayout();

		if ($layout == null || $layout === '') {
			echo $view,$js;
		}else{
			$_params_ = [
				'head' => $Controller->_getHead(),
				'title' => $Controller->_getTitle(),
				'view' => $view,
				'js' => $js
			];

			ob_start();
			ob_implicit_flush(false);
			extract($_params_, EXTR_OVERWRITE);
			require(APP_PATH . '/view/_layout/' . $layout . '.php');
			echo ob_get_clean();
		}
	}

}
