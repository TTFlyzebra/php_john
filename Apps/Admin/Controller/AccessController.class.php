<?php

namespace Admin\Controller;

use Think\Controller;
/**
 * 配置角色节点
 * @author FlyZebra
 *
 */
class AccessController extends AuthController {
	
	/**
	 * 分配权限页面
	 */
	public function index() {
		$r_id = I('pid');
		$this->r_id = $r_id;
		$access = M('access')->where(array('role_id'=>$r_id))->getField('node_id',true);
		$node = M ( 'node' )->select ();
		$node = node_merge($node,$access);
		$this->node = $node;
		$this->role = M ( 'role' )->select ();
		$this->access = M ( 'access' )->select ();
		$this->display ();
	}
	
	/**
	 * 分配权限页面
	 */
	public function setAccess(){
		$role_id = $_POST['pid'];
		$data = array();
		foreach ($_POST['nodeid'] as $v){
			$tmp = explode('_', $v);
			$data[] = array(
					'role_id'=>$role_id,
					'node_id'=>$tmp[0],
					'level'=>$tmp[1]
			);
		}
		$db= M('access');
		$db->where(array('role_id'=>$role_id))->delete();
		if($data==null){
			$this->redirect("/Admin/Access/index",array('pid' =>$role_id));
		}else if($db->addAll($data)){
			$this->redirect("/Admin/Access/index",array('pid' =>$role_id));
		}else{
			$this->error($db->getError());
		}
	
	}
}