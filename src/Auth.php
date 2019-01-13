<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/7 下午3:04
 */

namespace sugger\thinkAuth;


use sugger\thinkAuth\model\GroupRouteView;
use sugger\thinkAuth\model\ModelAct;
use sugger\thinkAuth\model\UserGroupView;

class Auth
{
	protected static $_user;
	protected static $_user_id;
	protected static $_user_group;
	protected static $_user_extra = [];



	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/11 下午4:13
	 * 描述：设置账户
	 *
	 * @param $user
	 */
	public static function setUser($user)
	{
		self::$_user = $user;
		$pk = $user->getPk();
		self::$_user_id = $user->{$pk};
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/11 下午4:15
	 * 描述：获取账户
	 *
	 * @return \think\Model
	 */
	public static function getUser()
	{
		return self::$_user;
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/12 上午9:46
	 * 描述：获取账号ID
	 *
	 * @return mixed
	 */
	public static function getUserID(){
		return self::$_user_id;
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/11 下午4:23
	 * 描述：检测登录
	 */
	public static function isLogin()
	{
		return boolval(self::$_user);
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/11 下午5:13
	 * 描述：获取用户所有的组据集
	 *
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function getUserGroupCollection(){
		if (self::$_user_group)
			return self::$_user_group;
		$user_id = self::$_user_id?:'%';
		return UserGroupView::getUserGroup($user_id);
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/11 下午5:19
	 * 描述：获取用户组ID
	 * @return array
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function getUserGroupId(){
		return array_column(static::getUserGroupCollection()->toArray(),'group_id');
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/7 下午11:12
	 * 描述：获取当前用户所有路由
	 * @throws \OAuthException
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function getUserAllRoute(){
		return GroupRouteView::getAllRouteByGroupId(self::getUserGroupId());
	}


	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/10 下午2:21
	 * 描述：获取用户的指定路由
	 *
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 *
	 * @return bool
	 * @throws \OAuthException
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function getRoute($action = '',$controller = '',$module = ''){
		if (self::isLogin() === false)
			return [];
		$request = request();
		if ($action === '') $action = $request->action();
		if ($controller === '') $controller = $request->controller();
		if ($module === '') $module = $request->module();
		$route = self::getUserAllRoute();
		$action = strtolower($action);
		$controller = strtolower($controller);
		$module = strtolower($module);
		$url = strtolower("$module/$controller/$action");
		if (empty($route[$url])) return false;
		return $route[$url];
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/10 下午2:57
	 * 描述：检测是否有权限访问路由
	 *
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 *
	 * @return bool
	 * @throws \OAuthException
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function checkRoute($action = '',$controller = '',$module = '')
	{
		$request = request();
		if ($action === '') $action = $request->action();
		if ($controller === '') $controller = $request->controller();
		if ($module === '') $module = $request->module();
		$url = strtolower("$module/$controller/$action");
		if (self::getRoute($action ,$controller ,$module)) return true;
		return false;
	}








	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/10 下午4:06
	 * 描述：获取显示字段
	 *
	 * @param string $action
	 * @param string $controller
	 * @param string $module
	 *
	 * @return array|mixed
	 * @throws \OAuthException
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function getShowFields($action = '',$controller = '',$module = ''){
		$request = request();
		if ($action === '') $action = $request->action();
		if ($controller === '') $controller = $request->controller();
		if ($module === '') $module = $request->module();
		$route = self::getRoute($action ,$controller ,$module);
		$fields = [];
		foreach ($route as $value){
			$fields = self::merge($fields,$value['show_fields']);
		}
		return $fields;
	}


	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/11 下午2:31
	 * 描述：获取用户数据的操作范围
	 *
	 * @param      $data
	 * @param null $model_class
	 *
	 * @return bool
	 * @throws \OAuthException
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function getDataAct($data,$model_class=null){
		if (is_null($model_class) && is_array($data))
			return false;
		if (is_null($model_class) && is_object($data))
			$model_class = get_class($data);
		$all_data_act = ModelAct::getRouteUrlByModelData($model_class);
		$all_act = self::getUserAllRoute();
		$data_act = [];
		foreach ($all_data_act as $url){
			if (isset($all_act[$url]))
				$data_act[] = $all_act[$url];
		}
		$return = [];

		foreach ($data_act as $more_access){
			foreach ($more_access as $one_access){
				if (empty($one_access['data_route_access']) || self::checkDataHasAct($data,$one_access['data_route_access'])){
					$return[$one_access['route_url']] = $one_access['route_name'];
					continue;
				}
			}
		}
		return $return;
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/11 下午2:31
	 * 描述：
	 *
	 * @param $data
	 * @param $data_route_access
	 */
	public static function checkDataHasAct($data,$data_route_access){

		$extend = self::isLogin()?self::getAllExtra():[];
		foreach ($data_route_access as $access){
			if ($access['VALIDATE_TYPE'] === 'VALUE'){
				$res = $data[$access['KEY']] == $access['VALUE'];
			}elseif ($access['VALIDATE_TYPE'] === 'FUNCTION'){
				$res = $access['VALUE']($data,$extend,self::$_user);
			}elseif ($access['VALIDATE_TYPE'] === 'CLASS'){
				$res = call_user_func($access['VALUE'],$data,$extend,self::$_user);
			}
			if (($access['RETURN_TYPE'] == 1 && $res === false) || ($access['RETURN_TYPE'] == 0 && $res === true)){
				return false;
			}
		}
		return true;


	}




	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/10 下午4:07
	 * 描述：合并多级数组
	 *
	 * @param $a
	 * @param $b
	 *
	 * @return array|mixed
	 */
	public static function merge($a, $b)
	{
		$args = func_get_args();
		$res = array_shift($args);
		while (!empty($args)) {
			foreach (array_shift($args) as $k => $v) {
				if ($v instanceof UnsetArrayValue) {
					unset($res[$k]);
				} elseif ($v instanceof ReplaceArrayValue) {
					$res[$k] = $v->value;
				} elseif (is_int($k)) {
					if (array_key_exists($k, $res)) {
						$res[] = $v;
					} else {
						$res[$k] = $v;
					}
				} elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
					$res[$k] = static::merge($res[$k], $v);
				} else {
					$res[$k] = $v;
				}
			}
		}

		return $res;
	}


	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/10 上午10:29
	 * 描述：获取用户权限验证的 拓展信息
	 *
	 * @param bool $reload 是否重新载入
	 *
	 * @return array
	 * @throws \OAuthException
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function getAllExtra($reload = false){
		if (self::$_user_extra && $reload === false){
			return self::$_user_extra;
		}
		$user_groups = self::getUserGroupCollection()->toArray();
		$extra = [];
		foreach ( $user_groups as  $user_group){
			if (empty($user_group['extra']))
				continue;
			foreach ($user_group['extra'] as $key=>$value){
				if (!isset($extra[$user_group['group_id']]))
					$extra[$user_group['group_id']] = [];
				if (isset($extra[$user_group['group_id']][$key])){//存在相同字段
					if (is_string($extra[$user_group['group_id']][$key])){
						if ($extra[$user_group['group_id']][$key] == $value)  continue;
						$extra[$user_group['group_id']][$key] = [$extra[$user_group['group_id']][$key],$value];
					}else{
						if (in_array($value,$extra[$user_group['group_id']][$key]))  continue;
						array_push($extra[$user_group['group_id']][$key],$value);
					}
					is_string($extra[$user_group['group_id']][$key]) && $extra[$user_group['group_id']][$key] = [$extra[$user_group['group_id']][$key]] || array_push($extra[$user_group['group_id']][$key],$value);
				}else{
					$extra[$user_group['group_id']][$key] = $value;
				}
			}
		}
		self::$_user_extra = $extra;
		return self::$_user_extra;
	}


	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/10 上午9:57
	 * 描述：获取指定权限组的拓展信息
	 *
	 * @param int $group_id
	 * @param bool $reload 是否重新载入
	 *
	 * @return bool
	 * @throws \OAuthException
	 * @throws \think\Exception
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public static function getUserGroupExtra(int $group_id,$reload = false){
		$allExtra = self::getUserGroupAllExtra($reload);
		return isset($allExtra[$group_id])?$allExtra[$group_id]:false;
	}



}