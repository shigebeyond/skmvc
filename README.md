# Skmvc
Skmvc is an elegant, powerful and lightweight MVC web framework built using kotlin. It aims to be swift, secure, and small. It will turn java's heavy development into kotlin's simple pleasure.

Inspired by 2 php frameworks: [kohana](https://github.com/kohana/kohana)

# usage - web

## 1 Configure .htaccess

```
cp example.htaccess .htaccess
```

## 2 Create Controller

Controller handles request, and render data to response.

It has property `req` to represent request, `res` to represent response.

In `application/classes/Controller` directory, you should create your own controller class:

```
/**
 * application/classes/Controller/Home.php
 */
class Controller_Home extends Controller
{
    public function action_index()
    {
        $this->res->body("hello world");
    }
}
```

## 3 Visit web page

visit http://localhost/skmvc/

![](https://github.com/shigebeyond/skmvc/blob/master/img/webpage.png)

# usage - view

## 1 Render View in Controller

```
public function action_view()
{
    // 创建视图
    $view = new View('test', array('name' => 'shi')); // 自定义视图, 视图文件 application/views/test.php
    // $view = $this->view(array('name' => 'shi')); // 默认视图, 视图文件 application/views/$controller/$action.php

    // 设置变量
    $view->set('age', 24);

    // 渲染
    // $this->res->body($view->render()); // 显示渲染视图: 调用view::render()
    $this->res->body($view); // 隐式渲染视图
}
```

## 2 Create view file

vim application/views/test.php

```
<?php
echo "hello $name, your age is: $age";
```

## 3 Visit web page

visit http://localhost/skmvc/welcome/view

![](https://raw.githubusercontent.com/shigebeyond/jkmvc/master/img/webview.png)


# usage - orm

Orm　provides object-oriented way to mainpulate db data.

It has 2 concepts:

1 Orm meta data: include information as follows

1.1 mapping from object to table

1.2 mapping from object's property to table's column

1.3 mapping from object's property to other object

2 Orm object | Model

2.1 visit property

you can use operator `->` to visit orm object's property.

2.2 method

`query_builder()` return a query builder to query data from table

`create()` create data

`update()` update data

`delete()` delete data

## 1 Create tables

user table

```
CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `age` smallint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';
```

contact table

```
CREATE TABLE `contact` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `email` varchar(254) NOT NULL,
  `address` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uniq_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='联系方式表';
```

## 2 Create Model

use model, extends Orm

```
/**
 * 用户模型
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-19 上午9:11:51  
 *
 */
class Model_User extends Orm
{
	/**
	 * 每个字段的校验规则
	 * @var array
	 */
	protected static $_rules = array(
		'name' => 'trim > not_empty && length(1, 10)',
		'age' => 'trim > is_numeric && range(0, 100)', // 1 trim > 修改值 2  is_numeric && range(0, 100) 校验值
	);
	
	/**
	 * 每个字段的标签（中文名）
	 * @var array
	 */
	protected static $_labels = array(
		'name' => '姓名',
		'age' => '年龄',
	);
	
	/**
	 * 关联关系
	 * @var array
	 */
	protected static $_relations = array(
		'contacts' => array( // 有多个联系方式
			'type' => Orm::RELATION_HAS_MANY, // 关联类型: 有多个
			'model' => 'Contact', // 关联模型
			'foreign_key' => 'user_id'	//外键
		)
	);
}
```

contact model, extends Orm

```
/**
 * 联系方式模型
 *
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-19 上午9:12:10  
 *
 */
class Model_Contact extends Orm
{
	/**
	 * 每个字段的校验规则
	 * @var array
	 */
	protected static $_rules = array(
			'email' => 'trim > not_empty && email',
			'address' => 'trim > not_empty && length(1, 10)',
	);
	
	/**
	 * 关联关系
	 * @var array
	 */
	protected static $_relations = array(
		'user' => array( // 从属于某个用户
			'type' => Orm::RELATION_BELONGS_TO, // 关联类型: 从属于
			'model' => 'User', // 关联模型
			'foreign_key' => 'user_id' // 外键
		)
	);
}
```

## 3 Use Model

```
class Test_Orm extends PHPUnit_Framework_TestCase
{
	public function test_orm(){
		$user = new Model_User(1);
		print_r($user->as_array());
		//$this->assertEquals(true, $user->exists());
	}
	
	public function test_exists(){
		$user = new Model_User(1);
		$this->assertEquals(true, $user->exists());
		$user = new Model_User();
		$this->assertEquals(false, $user->exists());
		$user = new Model_User(10000);
		$this->assertEquals(false, $user->exists());
	}
	
	public function test_insert(){
		$user = new Model_User();
		$user->name = 'shi';
		$user->age = 24;
		$user->create();
		print_r($user->as_array());
	}
	
	public function test_update(){
		$user = new Model_User(7);
		$user->name = 'wang';
		$user->age = 124;
		$user->update();
		print_r($user->as_array());
	}
	
	public function test_delete(){
		$user = new Model_User(4);
		if ($user->exists()) {
			print_r($user->as_array());
			$result = $user->delete();
			echo '删除成功';
			$this->assertEquals(1, $result);
		}else {
			echo '对象不存在';
		}
		
	} 
	
	public function test_find(){
		$user = Model_User::query_builder()->where('id', '=', 5)->find();
		if($user)
		{
			print_r($user->as_array());
			$this->assertEquals(7, $user->id);
		}
		else 
		{
			echo '没有找到记录';
		}
	} 
	
	public function test_find_all(){
		$users = Model_User::query_builder()->find_all();
		print_r($users);
	} 
}
```


# usage - odm(Object Document Mapping, ps: Mongodb's Document)

Odm　provides object-oriented way to mainpulate mongodb data.

It has 2 concepts:

1 Odm meta data: include information as follows

1.1 mapping from object to collection

1.2 mapping from object's property to collection's column

1.3 mapping from object's property to other object

2 Odm object | Model

2.1 visit property

you can use operator `->` to visit orm object's property.

2.2 method

`query_builder()` return a query builder to query data from table

`create()` create data

`update()` update data

`delete()` delete data

## 1 Create collections

Mongodb doesnot need to create collections in advance.

Just use it, it will be created automatically.

## 2 Create Model

Create models just like Orm.


## 3 Use Model

Use models just like Orm.

## 4 Why just like Orm

Thanks for the elegant encapsulation. Odm extends the same super class just like Orm. Odm just needs to override the following properties and methods:
 
1. `$_primary_key`: Mongodb's default primary key is `_id`
2. `db()`: `Mongoo` instance
3. `query_builder()`: `Odm_Query_Builder` instance

```
/**
 * ODM
 * 	实现：
 *     重用ORM的代码，只是变动部分属性 $_primary_key / 方法 db()
 *     重用关系型数据的概念，mongo的collection对应db的table
 *  
 *  关系型数据库与mongodb的3个层次的兼容：
 *    1 Db层：Db 与 Mongoo 不用兼容
 *    2 Query_Builder层：Db_Query_Builder 与 Mongoo_Query_Builder 尽量兼容
 *    3 ORM层：ORM 与 ODM 完全兼容，终极目标
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-11-5 上午1:23:05 
 *
 */
class Sk_Odm extends Orm_Related
{
	/**
	 * 主键
	 *     默认一样, 基类给默认值, 子类可自定义
	 * @var string
	 */
	protected static $_primary_key = '_id';
	
	/**
	 * 获得数据库
	 * @param string $action 命令动作：find/findOne/insert/update/delete，可以用于区分读写的数据库连接
	 * @return Mongoo
	 */
	public static function db($action = 'find')
	{
		if(!static::$_db instanceof Db)
			static::$_db = Mongoo::instance(static::$_db);
	
		return static::$_db;
	}
	
	/**
	 * 获得查询构建器
	 * @return Odm_Query_Builder
	 */
	public static function query_builder()
	{
		return new Odm_Query_Builder(get_called_class());
	}
}
```