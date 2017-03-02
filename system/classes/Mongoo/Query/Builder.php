<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Mongodb查询构建器
 *  方法与 Interface_Db_Query_Builder_Action + Interface_Db_Query_Builder 的差不多，兼容部分 Db_Query_Builder 的api
 *  主旨是方便 ODM （使用的是 Mongoo_Query_Builder 来操作数据） 直接重用 ORM（使用的是 Db_Query_Builder 来操作数据） 的实现
 *  首先追求在 ORM 层统一关系型数据库与mongodb
 *  其次尽量追求在 Query_Builder 层统一关系型数据库与mongodb，但是由于两类数据库api不尽相同，因此只是尽量兼容，无需全部兼容
 * 
 * @Package package_name 
 * @category 
 * @author shijianhang
 * @date 2016-10-28 下午10:18:27 
 *
 */
class Mongoo_Query_Builder extends Sk_Mongoo_Query_Builder {}