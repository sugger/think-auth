<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/6 下午4:28
 */

namespace sugger\thinkAuth\model;


use sugger\thinkAuth\Model;
use think\Cache;

class RoleRouteView extends Model
{

	protected $type = [
		'show_fields'=>'json',
		'data_scope'=>'json',
		'data_route_access'=>'json',
	];


	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/8 上午12:24
	 * 描述：获取指定权限组的所有权限
	 *
	 * @param array $roleIds
	 *
	 * @return array|mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function getAllRouteByRoleId($role_ids){
		if (empty($role_ids))
			return [];
		sort($role_ids);
		$role_ids = implode(',',$role_ids);
		if ($groups_route = Cache::get("role_route{$role_ids}")){
			return $groups_route;
		}
		$groups_route = static::where(['role_id'=>['in',$role_ids]])->select()->toArray();
		$tree = [];
		foreach ($groups_route as $value){
			$tree[$value['route_url']][] = $value;
		}
		Cache::set("role_route{$role_ids}",$tree,10);
		return  $tree;
	}

}