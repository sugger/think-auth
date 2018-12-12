<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/6 下午4:28
 */

namespace sugger\thinkAuth\model;


use sugger\thinkAuth\Model;
use think\Cache;

class GroupRouteView extends Model
{

	protected $type = [
		'show_fields'=>'json'
	];


	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/8 上午12:24
	 * 描述：获取指定权限组的所有权限
	 *
	 * @param $group_ids
	 *
	 * @return array|mixed
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function getAllRouteByGroupId($group_ids){
		sort($group_ids);
		$group_ids = implode(',',$group_ids);
		if ($groups_route = Cache::get("group_route{$group_ids}")){
			return $groups_route;
		}
		$groups_route = GroupRouteView::where(['group_id'=>['in',$group_ids]])->select()->toArray();
		$tree = [];
		foreach ($groups_route as $value){
			$tree[$value['route_url']][] = $value;
		}
		Cache::set("group_route{$group_ids}",$tree,10);
		return  $tree;
	}

}