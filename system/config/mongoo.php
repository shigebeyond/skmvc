<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'default' => array(
			 'server'	=> 'mongodb://localhost:27017',//'mongodb://192.168.0.17:27017',
	    'options'   => array(
					'db' => 'test',
// 					'username' => 'test', //　用户名
// 					'password' => 'sk123', //　密码
					//　'persist' => 'p', //　长连接的标识
					// 'replicaSet' => 'myReplSet',　//　
		    ),
			'debug' => true, //　是否打日志 
	  ),
);
