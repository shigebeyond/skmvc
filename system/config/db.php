<?php defined('SYSPATH') OR die('No direct script access.');

// 数据连接配置
return array (
		'master' /* 连接名 */ => array (
				'dsn' => 'mysql:host=localhost;dbname=test', // Data Source Name 数据源
				'username' => 'root', // 用户名
				'password' => 'r00tdb', // 密码
				'persistent' => FALSE, // 是否保持连接
				'table_prefix' => '', // 表前缀
				'charset' => 'utf8', // 字符集
				'caching' => FALSE  // 是否缓存查询结果
			) ,
		'slave' /* 连接名 */ => array (
				'dsn' => 'mysql:host=localhost;dbname=test', // Data Source Name 数据源
				'username' => 'root', // 用户名
				'password' => 'r00tdb', // 密码
				'persistent' => FALSE, // 是否保持连接
				'table_prefix' => '', // 表前缀
				'charset' => 'utf8', // 字符集
				'caching' => FALSE  // 是否缓存查询结果
			) ,
);