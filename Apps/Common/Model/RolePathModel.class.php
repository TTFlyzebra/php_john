<?php
namespace Common\Model;

use Think\Model\RelationModel;

class RolePathModel extends RelationModel{
	//定义主表
	protected $tableName = 'role';
	
	//定义关联关系
	protected $_link = array(
			'node'=>array(
					'mapping_type' => self::MANY_TO_MANY,
					'class_name'  =>  'node',
					'foreign_key'=>'role_id',
					'relation_key'=>'node_id',
					'relation_table'=>'jh_access',
					'mapping_fields'=>'id,name,pid'
			)
			
	);
}