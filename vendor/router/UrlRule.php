<?php
/**
 * 如果启用了路由重写功能
 * 则在此类中将访问路由与用户自定义的路由规则进行匹配
 */
class UrlRule{
	public $defCtrl;//config.php route controller 配置中默认的控制器
	public $defAct;//默认动作
	public static $rules;//路由规则 array

	public function __construct(&$routeConf){
		$this->defCtrl = $routeConf['controller'];
		$this->defAct = $routeConf['action'];
		static::$rules = $routeConf['rules'];
	}

	/**
	 * 对用户输入的网址进行解析，在路由中匹配是否存在对应的规则
	 */
	public function matchAllRules(){
		//从$_SERVER['REQUEST_URI']中，除去头部baseUrl部分，再去除首尾中的 / 字符
		$requestUri = trim(substr($_SERVER['REQUEST_URI'], strlen(YC::$baseUrl) + 1), '/');
		//把请求的URI中，?号及后面部分去掉
		if (strpos($requestUri, '?') !== false) {
			$requestUri = strstr($requestUri, '?', true);
		}
		//如果在config.php中设置的默认后缀，则去除后缀名
		if (!empty(RouteResponse::$suffix && ($index = strrpos($requestUri, '.')) !== false)) {
			$requestUri = substr($requestUri, 0, $index);
		}

		foreach (static::$rules as $key => $rule) {
			if (is_string($rule)) {
				if ($this->matchOneRule($requestUri, $rule)) {
					return $key;
				}
			}elseif (is_array($rule)){
				foreach ($rule as $v) {
					if (!is_string($v)) {
						throw new InvalidValueException('the value of rule must be a string or linear array');
					}
					if ($this->matchOneRule($requestUri, $v)) {
						return $key;
					}
				}
			}else{
				throw new InvalidValueException('the value of rule must be a string or linear array');
			}
			
		}
		return $requestUri;
	}

	/**
	 * 将一条URI与一条规则进行一对一的详细匹配
	 */
	public function matchOneRule($uri, $rule){
		$uriArray = explode('/', $uri);
		$ruleArray = explode('/', $rule);

		//比较URI与rule的长度，不匹配则返回
		$n = count($ruleArray);
		if ($n !== count($uriArray)) {
			return false;
		}

		$i = 0;
		$tempGet = [];
		while ($i < $n) {
			if (strpos($ruleArray[$i], '{') !== false) {
				/**
				 * 获取此条规则的格式，再与url匹配。
				 * 匹配不通过则返回false
				 */
				$patten = '/\{(.+?)\}/';
				$format = preg_split($patten, $rule);
				$_patten = array_shift($format);
				foreach ($format as $key => $value) {
					$_patten .= '(.+?)' . $value;
				}
				$_patten = '/^'. addcslashes($_patten, '/') .'$/';
				preg_match_all($_patten, $uri, $splitParam);
				if (empty(array_shift($splitParam)[0])) {
					return false;
				}
				/**
				 * 取出url中进行重写的get参数并进行验证
				 * 不符合路由格式则返回false
				 */
				$len = count($splitParam);
				preg_match_all($patten, $rule, $splitRule);

				$getParams = [];
				for ($n=0; $n < $len; ++$n) {
					if (strpos($splitRule[1][$n], ':') !== false) {
						$get = explode(':', $splitRule[1][$n]);
						if(YC::$app->paramFilter->isValid($splitParam[$n][0], $get[1]) === false)
							return false;
						$getParams[$get[0]] = $splitParam[$n][0];
					}else
						$getParams[$splitRule[1][$n]] = $splitParam[$n][0];
				}
				$_GET = array_merge($_GET, $getParams);
				return true;
				
			}elseif ($uriArray[$i] === $ruleArray[$i]) {
				return true;
			}else
				return false;
			++ $i;

		}
		
	}

}