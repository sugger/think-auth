<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/6 下午4:28
 */

namespace sugger\thinkAuth\model;


use sugger\thinkAuth\Model;

class UserGroupView extends Model
{


	protected $type = [
		'extra'      =>  'json',
	];

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/11 下午5:10
	 * 描述：获取用户权限组
	 *
	 * @param $user_id
	 *
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function getUserGroup($user_id){
		return self::where(['user_id'=>$user_id])->select();
	}


}