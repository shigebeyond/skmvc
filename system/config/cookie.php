<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'expiration' => 0, // 过期时间
	'path' => '/', // 路径
	'domain' => NULL, // 域名
	'secure' => FALSE, // 是否只对https有效
	'httponly' => FALSE, // 是否只对http有效，对js无效
);