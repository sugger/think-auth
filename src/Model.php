<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/6 下午2:56
 */

namespace sugger\thinkAuth;


abstract class Model extends \think\Model
{
	protected $connection = [
		// 数据库类型
		'type'            => 'mysql',
		// 服务器地址
		'hostname'        => '127.0.0.1',
		// 数据库名
		'database'        => 'vuethink',
		// 用户名
		'username'        => 'root',
		// 密码
		'password'        => '123456',
		// 端口
		'hostport'        => '3306',

		'dsn'             => '',
		// 数据库连接参数
		'params'          => [],
		// 数据库编码默认采用utf8
		'charset'         => 'UTF8',
		// 数据库表前缀
		'prefix' => 'auth_',
		// 数据集返回类型
		'resultset_type'  => '\think\Collection',

		// 时间字段取出后的默认时间格式
		'datetime_format' => 'Y-m-d H:i:s',

	];



}