<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * 申核权限
 * @author FlyZebra
 *
 */
class VerifyController extends AuthController {
	
	/**
	 * 申核权限页面
	 */
	public function index() {
		$userArr=D('UserRole')->field('loginword',true)->relation('role')->select();
		foreach ( $userArr as $user ) {
			$user['role_id'] = $user['role'][0]['id'];
			$user['role_remark'] = $user['role'][0]['remark'];
			unset($user['role']);
			$data[]=$user;
		}
		$this->user=$data;
		$this->role=M('role')->select();
		$this->display ();
	}
	
	/**
	 * 修改用户权限
	 */
	public function verify(){
		$data = $_POST;
		$db = M('role_user');
		$db->where(array('user_id'=>$data['user_id']))->delete();
		if($data['role_id']==null) die("no role_id");
		if($db->add($data)){
			die("succeed");
		}else{
			die("failed");
		}
	}
	
}