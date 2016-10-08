<?php defined('SYSPATH') OR die('No direct script access.');

// 路由规则配置
return array(
	// 1 自带参数的正则
	'/(?P<controller>.+)\/(?P<action>.+)\/(?P<id>\d+)/',
	
	// 2 正则 + 参数映射函数（从正则匹配结果中获得参数）
	'/(.+)\/(.+)\/(\d+)/' => function($mathes){
		return array(
			'controller' => $mathes[1],
			'action' => $mathes[2],
			'id' => $mathes[3],
		);
	},
	
	// 3 正则 + 预设参数
	'/.+/' => function($mathes){
		return array(
			'controller' => 'Home',
			'action' => 'index',
		);
	},
);