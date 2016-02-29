<?php
return [
	'html_charset' => 'utf-8',
	'classMap' => [
		
	],

	'classFolder' => [
		APP_PATH . '/class'
	],

	'route' => [
		'enableRewrite' => true,
		'suffix' => null,
		'rules' => [
			'user/login' => 'login'
			// 'index/index' => [
			// 	'{id:\d+}_{page}',
			// 	'index/{id:\d+}-{page}'
			// ],
			// 'index/test' => ['test/{id:\d+}_{ids:\d+}_{dd}','test/{id:\d+}_{ids:\d+}'],
			// 'login/index' => 'login',
			// 'article/read' => 'article/{id:\d+}'
		],
	],

	
];

