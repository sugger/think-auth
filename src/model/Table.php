<?php
/**
 * 创建人: 王磊
 * 创建时间: 2018/12/6 下午4:28
 */

namespace sugger\thinkAuth\model;


use sugger\thinkAuth\Model;

class Table extends Model
{
	protected $table = 'auth_table';
	protected $type = [
		'fields'      =>  'json',
	];
}