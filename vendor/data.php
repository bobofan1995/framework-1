<?php
return [
	'classMap' => [
		'yc\base\Controller'    => VENDOR_PATH . '/base/Controller.php',
		'Controller' => APP_PATH . '/component/Controller.php',
		'Model'         => '/db',
		'ComponentLoader'    => '/base',
		'Request'       => '/base',
		'RouteResponse' => '/router',
		'UrlRule'       => '/router',
		'ParamFilter'   => '/component',
		'UrlBuild'      => '/router',
		'View'          => '/base',
		'InvalidValueException' => '/exception',
		'ExceptionInterface'    => '/exception',
		'DBConnection' => '/db',
		'DB' => '/db',
		'DBCommand'	=> '/db',
		'DBException' => '/exception',
		'Middleware' => APP_PATH . '/component/Middleware.php',
		'Upload' => '/component'
	],

	'classFolder' => [
		APP_PATH . '/model',
		APP_PATH . '/component',
		APP_PATH . '/controller'
	],
	
	'layout' => 'main',

	'route' => [
		'controller' => 'index',
		'action' => 'index'
	]
];
