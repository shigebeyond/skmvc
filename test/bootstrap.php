<?php

// 定义相关目录的常量
define('ROOTPATH', realpath(dirname(__FILE__).'/../').DIRECTORY_SEPARATOR);
define('APPPATH', ROOTPATH.'application'.DIRECTORY_SEPARATOR);
define('SYSPATH', ROOTPATH.'system'.DIRECTORY_SEPARATOR);
define('TESTPATH', ROOTPATH.'test'.DIRECTORY_SEPARATOR);

// 自动加载
require SYSPATH.'classes/Sk/Loader.php';
spl_autoload_register(array('Sk_Loader', 'load_class'));

// 添加测试的顶级目录
Loader::add_path(TESTPATH);