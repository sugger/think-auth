<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/7 下午3:03
 */

namespace sugger\thinkAuth;

use sugger\thinkAuth\model\Route as RouteModel;
use think\Cache;

class Route extends Base
{
	
	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/10 下午2:56
	 * 描述：获取不需要登录的路由
	 *
	 * @return array
	 */
	public function getNotNeedLoginRoute(){
		$cache_key = 'auth_getNotNeedLoginRoute';
		if ($res = Cache::get($cache_key)) return $res;
		$cond = [
			'need_login'=>'0',
			'route_level'=>'3',
		];
		$res = RouteModel::where($cond)->select()->toArray();
		$res = $this->getTree($res);
		if ($res) Cache::set($cache_key,$res,10);
		return $res;
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/10 下午3:06
	 * 描述：获取不需要登录的路由URL
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getNotNeedLoginRouteUrl(){
		$cache_key = 'auth_getNotNeedLoginRouteUrl';
		if ($res = Cache::get($cache_key)) return $res;
		$res = RouteModel::where("need_login = '0'")->column('route_url');
		if ($res) Cache::set($cache_key,$res,10);
		return $res;
	}


	public function getRouteByModelData($model_class){
		$cache_key = "auth_routeByModelData_$model_class";
		if ($res = Cache::get($cache_key)) return $res;
		$res = RouteModel::where("need_login = '0'")->column('route_url');

	}
}