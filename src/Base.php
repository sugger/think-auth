<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/7 下午2:53
 */

namespace sugger\thinkAuth;


abstract class Base
{

	/**
	 * 容器对象实例
	 * @var Container
	 */
	protected static $instance;


	/**
	 * 获取当前容器的实例（单例）
	 * @access public
	 * @return static
	 */
	public static function getInstance()
	{
		$instance = new static();
		$className = get_class($instance);
		if (!isset(static::$instance[$className])) {
			static::$instance[$className] = $instance;
		}
		unset($instance);
		return static::$instance[$className];
	}





}