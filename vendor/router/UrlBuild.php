<?php
/**
 * 根据应用设置的路由规则生成链接
 */
class UrlBuild{
	public $webUrl;

	public function __construct(){
		$this->webUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
	}

	/**
	 * @param path string , request path of controller and action
	 * @param param parameter of url
	 * @return string
	 */
	public function urlTo($path, $params = []){
		if (RouteResponse::$enableRewrite === true) {
			 return $this->matchWithRules($path, $params);
		}else{
			return $this->urlNormal($path, $params);
		}
	}

	//if rewrite on...
	protected function matchWithRules($path, array $params){
		/**
		 * 在rules中寻找路由规则提交到urlRewrite()进行重写
		 */
		if (isset(UrlRule::$rules[$path])) {
			if (is_string(UrlRule::$rules[$path])) {
				$result = $this->urlRewrite(UrlRule::$rules[$path], $params);
			}elseif(is_array(UrlRule::$rules[$path])){
				foreach (UrlRule::$rules[$path] as $value) {
					if (!is_string($value)) {
						throw new InvalidValueException('the value of rule must be a string or linear array');
					}
					$result = $this->urlRewrite($value, $params);
					if ($result !== false) {
						break;
					}
				}
			}else
				throw new InvalidValueException('the value of rule must be a string or linear array');
		}else{
			$result = false;
		}
		/**
		 * 在rules中没有找到满足条件的路由，则生成一般路由
		 */
		if ($result === false) {
			$paramOfUrl = '';
			if (!empty($params)) {
				$paramOfUrl = '?' . $this->createParams($params);
			}
			$result = [$path, $paramOfUrl];
		}
		/**
		 * 加后缀
		 */
		if (!empty(RouteResponse::$suffix)) {
			$result[0] .= RouteResponse::$suffix;
		}
		/**
		 * 把主路径与参数连接后返回
		 */
		return $result[0] . $result[1];
	}

	protected function urlRewrite($rule, array $params){
		$mainUrl = $rule;
		/**
		 * 如果url中存在需要替换的get参数...
		 */
		if (strpos($rule, '{')) {
			$patten = '/\{(.+?)\}/';
			preg_match_all($patten, $mainUrl, $splitRule);
			// echo '<pre>'; print_r($splitRule);exit();
			//需替换的参数是否有取值范围限制
			foreach ($splitRule[1] as $key => $value) {
				/**
				 * 若路由规则中对参数进行约束，则检查是否符合取值范围
				 */
				$constraint = null;//参数约束,d+/d...
				/**
				 * 若有进行约束，解析数值后对参数值value及约束值constraint重新进行赋值
				 */
				if (strpos($value, ':')) {
					$splitParam = explode(':', $value);
					$value = $splitParam[0];
					$constraint = $splitParam[1];
				}

				if (isset($params[$value])) {
					if( YC::$app->paramFilter->isValid($params[$value], $constraint) === false) {
						return false;
					}
					$splitRule[1][$key] = $params[$value];
					$mainUrl = str_replace($splitRule[0][$key], $params[$value], $mainUrl);
					unset($params[$value]);
				}else
					return false;
			}
		}
		
		$paramOfUrl = '';
		if (!empty($params)) {
			$paramOfUrl = '?' . $this->createParams($params);
		}

		return [$mainUrl, $paramOfUrl];
	}

	/**
	 * 若没有开启路由重写功能
	 * 或开启了但规则中没对应的重写项，则用此方法生成一般路由
	 */
	protected function urlNormal($path, $params = []){
		$urlParam = $this->createParams($params);
		return $this->webUrl . '?yc=' . $path . $urlParam;
	}

	//make the parameters of url
	protected function createParams($params = []){
		$urlParam = '';
		foreach ($params as $key => $value) {
			$urlParam .= $key . '=' . $value . '&';
		}
		$urlParam = rtrim($urlParam, '&');
		return $urlParam;
	}

	
}
