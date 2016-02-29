<?php
return [
	'type' => 'mysql',
	
	'host' => 'localhost',
	'port' => 3306,
	'dbname' => null,
	'user' => 'root',
	'password' => '',

	'charset' => 'UTF8',

	'pdo_fetch' => PDO::FETCH_ASSOC,
	'table_prefix' => null,

	//分表
	'submeter' => 10,
	'delimiter' => '_',

	//表关联
	'relevances' => [
		// 'post&user' => ['user_id', 'id'],
		// 'comment&post' => ['id', 'post_id'],
		// 'comment&user' => ['user_id', 'id']
	]
];