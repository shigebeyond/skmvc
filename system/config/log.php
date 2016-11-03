<?php defined('SYSPATH') OR die('No direct script access.');

// 日志配置
return array (
	'default' => array(
			'file' => ':date.log', // 日志文件名
			'threshold' => 1, // 日志输出的最低级别
			'date_format' => 'Y-m-d H:i:s', // 日期格式
			'log_format' => ":level\t:date\t:uri\t:msg\n", // 日志格式
	)
);