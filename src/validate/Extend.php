<?php
/**
 * 创建人: 王磊
 * 创建时间: 2019/1/13 下午11:53
 */

namespace sugger\thinkAuth\validate;


class Extend
{
	public static function depertment($data,$extend,$user){
		$departmet = array_column($extend,'department');
		return in_array($data['depertnemt'],$departmet);
	}
	public static function username($data,$extend,$user){
		return true;
	}
}