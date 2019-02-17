<?php
/**
 * 单一角色验证器，一个账户只有一种角色
 * 创建人: 王磊
 * 创建时间: 2019/2/16 下午4:22
 */

namespace sugger\thinkAuth;


class SingleRoleAuth extends Auth
{
	public function getUserRoleId()
	{
		return ['3','4'];
	}
	public function getUserInfoToAuthorization()
	{
		return ['collegeId'=>'123'];
	}
}