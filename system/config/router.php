<?php defined('SYSPATH') OR die('No direct script access.');

// 路由规则配置, 可写多个规则, 将按顺序来解析uri, 直到解析成功为止
return array(
	//1 完整的配置
	'(<controller>(\/<action>(\/<id>)?)?)?' => // uri的正则
		array( // 参数的配置, 可省略
			'params' => array( // 参数的子正则, 可省略
				'controller' => '[a-z]+',
				'action' => '[a-z]+',
				'id' => '\d+',
			),
			'defaults' => array( // 参数的默认值, 可省略
				'controller' => 'Home',
				'action' => 'index',
			)
	),
	/* 
	// 2 省略了参数配置
	'(<controller>(\/<action>(\/<id>)?)?)?', // uri的正则
	//3 省略了参数子正则的配置, 默认的参数子正则为 [^\/]+
	'(<controller>(\/<action>(\/<id>)?)?)?' => // uri的正则
	array( // 参数的配置
			'defaults' => array( // 参数的默认值
					'controller' => 'Home',
					'action' => 'index',
			)
	),
	//4 省略了参数默认值的配置, 前提是假定从uri能解析出controller/action等参数, 否则路由解析失败
	'(<controller>(\/<action>(\/<id>)?)?)?' => // uri的正则
	array( // 参数的配置
			'params' => array( // 参数的子正则
					'controller' => '[a-z]+',
					'action' => '[a-z]+',
					'id' => '\d+',
			),
	), 
	*/
);