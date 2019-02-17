<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/6 下午4:28
 */

namespace sugger\thinkAuth\model;


use sugger\thinkAuth\Model;
use think\Cache;

class ModelAct extends Model
{
	protected $table = 'auth_model_act';

	protected $type = [
		'model_act'=>'json'
	];

	public static function getRouteUrlByModelData($model_class){

		$cache_key = "auth_routeByModelData";
		if ($res = Cache::get($cache_key,[]) && isset($res[$model_class])) return $res[$model_class];
		$dbData = ModelAct::where(["model_class"=>$model_class])->field('model_class,model_name,model_act')->find();
		if ($dbData)
			$res[$model_class] = $dbData->toArray();
		if ($res) Cache::set($cache_key,$res,10);
		return !empty($res[$model_class])?$res[$model_class]['model_act']:[];
	}
}