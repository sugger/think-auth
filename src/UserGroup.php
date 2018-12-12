<?php
/**
 * 创建人: 用户分组管理器
 * 创建时间: 2018/12/7 下午3:00
 */

namespace sugger\thinkAuth;


use sugger\thinkAuth\model\UserGroup as UserGroupModel;
use think\Collection;
use think\Config;
use sugger\thinkAuth\model\Group as GroupModel;
use think\Exception;

class UserGroup extends Base
{
	/**
	 * @var Collection
	 */
	protected $_user_group;


	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/10 上午10:24
	 * 描述：设置权限组
	 *
	 * @param $userGroup
	 *
	 * @return bool
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function setGroups($userGroup){
		if ($userGroup instanceof Collection){
			$this->_user_group = $userGroup;
		}elseif(is_array($userGroup)){
			$this->_user_group = \sugger\thinkAuth\model\UserGroup::where($userGroup)->select();
		}else{
			return false;
		}
		return true;
	}

	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/10 上午10:05
	 * 描述：给用户添加权限组
	 */
	public function addGroup(\sugger\thinkAuth\model\UserGroup $userGroup){
		$this->_user_group->merge($userGroup);
	}
	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/7 下午3:53
	 * 描述：获取当前权限组
	 *
	 * @return array|mixed
	 * @throws Exception
	 * @throws \OAuthException
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getUserGroupList(){
		if ($this->_user_group)
			return $this->_user_group;
		$user = Auth::getInstance()->getUser();
		if (empty($user))
			throw new Exception('不存在的授权账户');
		$getGroupListByUser = Config::get('auth.getGroupListByUser');
		if (empty($getGroupListByUser)){
			$pk = $user->getPk();
			$this->_user_group = UserGroupModel::where(['user_id'=>$user->{$pk}])->where('end_timestamp','>=',date('Y-m-d H:i:s'))->select();
		}else if (is_array($getGroupListByUser) && count($getGroupListByUser) === 2){
			$this->_user_group = $getGroupListByUser[0]::$getGroupListByUser[1]($user);
		}else if (is_string($getGroupListByUser) || $getGroupListByUser instanceof \Closure){
			$this->_user_group = $getGroupListByUser($user);
		}else{
			throw new Exception('权限配置错误');
		}
		return $this->_user_group;
	}


	/**
	 * 创建人：王磊
	 * 创建时间：2018/12/7 下午5:40
	 * 描述：返回权限组ID
	 * @return mixed
	 * @throws Exception
	 * @throws \OAuthException
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getUserGroupIds(){
		return $this->getUserGroupList()->column('group_id');
	}





}