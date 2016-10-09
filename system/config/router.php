<?php defined('SYSPATH') OR die('No direct script access.');

// 路由规则配置
return array(
	'<controller>(\/<action>(\/<id>)?)?' => // uri的正则
		array(
			'params' => array( // 参数的子正则
				'controller' => '[a-z]+',
				'action' => '[a-z]+',
				'id' => '\d+',
			),
			'defaults' => array( // 参数的默认值
				'controller' => 'Home',
				'action' => 'index',
			)
	),
);