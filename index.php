<?php

// 定义环境常量
define('ENV', 'debug');
define('DEBUG', ENV == 'debug');

// 定义相关目录的常量
define('ROOTPATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH.'application'.DIRECTORY_SEPARATOR);
define('SYSPATH', ROOTPATH.'system'.DIRECTORY_SEPARATOR);

// 配置错误输出
if(DEBUG)
	error_reporting(E_ALL | E_STRICT);
else 
	error_reporting(E_ALL ^ E_NOTICE);

// 自动加载
require SYSPATH.'classes/Sk/Loader.php';
spl_autoload_register(array('Sk_Loader', 'load_class'));

if (PHP_SAPI == 'cli') // 命令行
{
	// TODO: 处理单元测试与任务
	//Cli::run();
}
else // web
{
	//处理请求
	Server::run();
}
