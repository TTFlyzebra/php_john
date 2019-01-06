<?php

namespace Admin\Controller;

use Think\Controller;

class NodeController extends AuthController {
	
	/**
	 * 显示节点管理页面
	 */
	public function index() {
		$node = M ( 'node' )->select ();
// 		$showArray = array();
// 		foreach($node as $temp){
			
// 		}
// 		$this->showdata = $node;
		$node=node_merge($node);
		$this->node=$node;
		$this->display ();
	}
	
	
	/**
	 * 添加节点
	 */
	public function add() {
		if (! IS_POST)$this->error ( "页面不存在！" );
		if ($_POST ['node_name'] == null 
				|| $_POST ['node_status'] == null 
				|| $_POST ['node_remark'] == null
				|| $_POST ['node_pid'] == null
				|| $_POST ['node_level'] == null) {
			$this->error ( "传入参数错误！" );
		}
		$node = array (
				'name' => $_POST ['node_name'],
				'status' => $_POST ['node_status'],
				'remark' => $_POST ['node_remark'],
				'pid' => $_POST ['node_pid'],
				'level' => $_POST ['node_level']
		);
		if (M ( 'node' )->add ( $node )) {
			$this->redirect ( "/Admin/node" );
		} else {
			$this->error ( "添加节点错误！" );
		}
	}
	
	
	/**
	 * 修改节点
	 */
	public function edit() {
		if (! IS_POST)$this->error ( "页面不存在！" );
		if ($_POST ['node_id'] == null
				||$_POST ['node_name'] == null
				|| $_POST ['node_status'] == null
				|| $_POST ['node_remark'] == null
				|| $_POST ['node_pid'] == null
				|| $_POST ['node_level'] == null) {
					$this->error ( "传入参数错误！" );
				}
				$node = array (
						'id'=>$_POST ['node_id'],
						'name' => $_POST ['node_name'],
						'status' => $_POST ['node_status'],
						'remark' => $_POST ['node_remark'],
						'pid' => $_POST ['node_pid'],
						'level' => $_POST ['node_level']
				);
				if (M ( 'node' )->save ( $node )) {
					$this->redirect ( "/Admin/node" );
				} else {
					$this->error ( "修改结点失败！" );
				}
	}
	
	
	/**
	 * 删除节点
	 */
	public function del() {
		if (! IS_POST)	$this->error ( "页面不存在！" );
		$node = array (
				'id' => $_POST ['id'],
				'name' => $_POST ['name'],
				'status' => $_POST ['status'],
				'remark' => $_POST ['remark'],
		);
		M ( 'node' )->where (array($node ))->delete ();
	}
	
	
	/**
	 * 修改节点状态
	 */
	public function status() {
		if (! IS_POST)	$this->error ( "页面不存在！" );
		$node = array (
				'id' => $_POST ['id'],
				'name' => $_POST ['name'],
				'status' => $_POST ['status'],
				'remark' => $_POST ['remark'],
		);
		M ( 'node' )->where($node)->save(array('status'=>!$node['status']));
	}
	
}