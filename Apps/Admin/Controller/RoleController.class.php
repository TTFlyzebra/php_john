<?php

namespace Admin\Controller;

use Think\Controller;

class RoleController extends AuthController {
	
	/**
	 * 显示角色管理页面
	 */
	public function index() {
		$this->role = M ( 'role' )->select ();
		$this->display ();
	}
	
	/**
	 * 添加角色
	 */
	public function add() {
		if (! IS_POST)
			$this->error ( "页面不存在！" );
		if ($_POST ['role_name'] == null || $_POST ['role_status'] == null || $_POST ['role_remark'] == null) {
			$this->error ( "传入参数错误！" );
		}
		$role = array (
				'name' => $_POST ['role_name'],
				'status' => $_POST ['role_status'],
				'remark' => $_POST ['role_remark'] 
		);
		if (M ( 'role' )->add ( $role )) {
			$this->redirect ( "/Admin/Role" );
		} else {
			$this->error ( "添加角色错误！" );
		}
	}
	
	/**
	 * 删除角色
	 */
	public function del() {
		if (! IS_POST)	$this->error ( "页面不存在！" );
		$role = array (
				'id' => $_POST ['id'],
				'name' => $_POST ['name'],
				'status' => $_POST ['status'],
				'remark' => $_POST ['remark'] 
		);
		M ( 'role' )->where (array($role ))->delete ();
	}
	
	
	/**
	 * 改变角色状态
	 */
	public function status() {
		if (! IS_POST)	$this->error ( "页面不存在！" );
		$role = array (
				'id' => $_POST ['id'],
				'name' => $_POST ['name'],
				'status' => $_POST ['status'],
				'remark' => $_POST ['remark']
		);
		M ( 'role' )->where($role)->save(array('status'=>!$role['status']));
	}
}