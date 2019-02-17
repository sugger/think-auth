<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/7 下午3:04
 */

namespace sugger\thinkAuth;


use sugger\thinkAuth\model\RoleRouteView;
use sugger\thinkAuth\model\ModelAct;
use sugger\thinkAuth\model\UserRoleView;

abstract class Auth
{
	protected  $_user;
	protected  $_user_id;


	abstract public function  getUserRoleId();
	abstract public function  getUserInfoToAuthorization();

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/11 下午4:13
	 * 描述：设置账户
	 *
	 * @param $user
	 */
	public function setUser($user)
	{
		$this->_user = $user;
		$pk = $user->getPk();
		$this->_user_id = $user->{$pk};
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/11 下午4:15
	 * 描述：获取账户
	 *
	 * @return \think\Model
	 */
	public function getUser()
	{
		return $this->_user;
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/12 上午9:46
	 * 描述：获取账号ID
	 *
	 * @return mixed
	 */
	public function getUserID(){
		return $this->_user_id;
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/11 下午4:23
	 * 描述：检测登录
	 */
	public function isLogin()
	{
		return boolval($this->_user);
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
	private function getUserAllRoute(){
		return RoleRouteView::getAllRouteByRoleId($this->getUserRoleId());
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
	public function getRoute($action = '',$controller = '',$module = ''){
		if (self::isLogin() === false)
			return [];
		$route = self::getUserAllRoute();
		$url = self::getDefaultActionStr($action,$controller,$module);
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
	public function checkRoute($action = '',$controller = '',$module = '')
	{
		echo $url = self::getDefaultActionStr($action,$controller,$module);
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
	public function getShowFields($action = '',$controller = '',$module = ''){
		$route = self::getRoute($action ,$controller ,$module);
		if (empty($route)) return [];
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
	public function getDataAct($data,$model_class=null){
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
	public function checkDataHasAct($data,$data_route_access){

		$extend = self::isLogin()?self::getUserInfoToAuthorization():[];
		foreach ($data_route_access as $access){
			if ($access['VALIDATE_TYPE'] === 'VALUE'){
				$res = $data[$access['KEY']] == $access['VALUE'];
			}elseif ($access['VALIDATE_TYPE'] === 'FUNCTION'){
				$res = $access['VALUE']($data,$extend,$this->_user);
			}elseif ($access['VALIDATE_TYPE'] === 'CLASS'){
				$res = call_user_func($access['VALUE'],$data,$extend,$this->_user);
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
	protected function merge($a, $b)
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


	private function getDefaultAction($action = '',$controller = '',$module = ''){
		$request = request();
		if ($action === '') $action = $request->action();
		if ($controller === '') $controller = $request->controller();
		if ($module === '') $module = $request->module();
		return [$action,$controller,$module];
	}
	private function getDefaultActionStr($action = '',$controller = '',$module = ''){
		$arr = self::getDefaultAction($action,$controller,$module);
		return "{$arr['2']}/{$arr['1']}/{$arr['0']}";
	}

}