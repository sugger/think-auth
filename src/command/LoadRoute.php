<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/12 上午10:19
 */

namespace sugger\thinkAuth\command;
use sugger\thinkAuth\model\Route;
use sugger\thinkAuth\model\Table;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class LoadRoute extends Command
{
	public function configure()
	{
		$this->setName('auth:load_route')->setDescription('load route apidoc api_data.json');
		$this->addArgument('path');
	}


	public function execute(Input $input, Output $output)
	{
		$path = $input->getArgument('path');
		if (!file_exists($path)) exit('文件不存在');
		$js = file_get_contents($path);
		$api_data = [];
		foreach (json_decode($js,true) as $api){
			list($module,$controller,$action) = array_values(array_filter(explode('/',$api['url'])));
			$api_data[$module][$controller][$action] = $api['title'];
		}
		$model = new Route();
		$model->startTrans();

		$model->execute('truncate '.$model->getTable());
		foreach ($api_data as $module_name=>$module_data){
			$model_db_data = ['module'=>$module_name,'route_level'=>'1'];
			$module_id =   $model->where($model_db_data)->value('route_id');
			if (empty($module_id)){
				$model->isUpdate(false)->data($model_db_data)->save();
				$module_id = $model->route_id;
			}

			foreach ($module_data as $controller_name=>$controller_data){
				$controller_db_data = array_merge($model_db_data,['controller'=>$controller_name,'pid'=>$module_id,'route_level'=>'2']);
				$controller_id =   $model->where($controller_db_data)->value('route_id');
				if (empty($controller_id)){
					$model->isUpdate(false)->data($controller_db_data)->save();
					$controller_id = $model->route_id;
				}

				foreach ($controller_data as $action_name=>$api_name){
					$action_data = array_merge($controller_db_data,[
						'action'=>$action_name,
						'pid'=>$controller_id,
						'route_name'=>$api_name,
						'route_url'=>strtolower("$module_name/$controller_name/$action_name"),
						'route_level'=>'3'
					]);
					$action_id =   $model->where(['route_url'=>$action_data['route_url']])->value('route_id');
					if (empty($action_id)){
						$model->isUpdate(false)->data($action_data)->save();
					}

				}
			}
		}
		$model->commit();
		$output->info('更新成功');
		exit;
	}
}