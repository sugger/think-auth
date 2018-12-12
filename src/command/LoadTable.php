<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/6 上午11:52
 */

namespace sugger\thinkAuth\command;

use sugger\thinkAuth\model\Table;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;
class LoadTable extends Command
{
	public function configure()
	{
		$this->setName('auth:load_table')->setDescription('load database table fields');
	}

	public function execute(Input $input, Output $output)
	{
		$model = new Table();
		//echo "select table_name,table_comment from information_schema.TABLES where table_schema = '{$model->getConfig('database')}'";die;
		$tables = $model->query("select table_name,table_comment,engine from information_schema.TABLES where table_schema = '{$model->getConfig('database')}'");

		$model->startTrans();

		$model->execute('truncate '.$model->getTable());
		foreach ($tables as $table){
			$tableName = $table['table_name'];
			$fields = $model->query("show full columns from $tableName");
			$fields = array_map(function ($field){
				unset($field['Privileges'],$field['Collation']);
				return $field;
			},$fields);
			$insertData = [
				'table_name'=>$tableName,
				'comment'=>$table['table_comment'],
				'fields'=>$fields,
				'engine'=>$table['engine'],
			];
			if (!$model->isUpdate(false)->save($insertData)){
				$model->rollback();
				$output->info('数据写入失败：' . $model->getLastSql());
				exit;
			}
			$output->info("表信息{$tableName}（{$table['table_comment']}）成功");

		}
		$model->commit();
		$output->info('成功写入'.count($tables).'个表信息');
		exit;
	}
}