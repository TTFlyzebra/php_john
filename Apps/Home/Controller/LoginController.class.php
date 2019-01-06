<?php

namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller {
	public function index() {
		$this->display ();
	}
	
	public function login(){
		if(!IS_POST) $this->error("页面不存在！");
		$db = M('user');
		$data = $db->where(array('loginname'=>$_POST['loginname'],'loginword'=>md5($_POST['password'])))->find();
		if($data==null) $this->error("用户名密码错误!");
		//添加记录登陆信息代码；
		import("Cls.User",APP_PATH);
		$log = \User::insertLog('user_log',$data['id'],'user_id');
		
		if(!$log) $this->error("更新登陆信息失败！");
		
		//session
		session(C('USER_AUTH_KEY'),$data['id']);
		session('loginname',$data['loginname']);
		session('photo',$data['photo']);
		session('dept_id',$data['dept_id']);
		session('loginIP',$log['loginIP']);
		session('logintime',$log['logintime']);
		
		$dept = M('dept')->where(array('id'=>$_SESSION['dept_id']))->find();
		session('deptname',$dept['deptname']);
		
		//如果是超级管理员，不需要认证
		if($data['loginname']==C('RBAC_SUPERADMIN')){
			session(C('ADMIN_AUTH_KEY'),true);
		}
		//RBAC
		\Org\Util\Rbac::saveAccessList();
		
		$this->redirect("/");//跳转到主页
	}
	
	public function logout(){
		session_unset();
		session_destroy();
		$this->redirect("/Home/Login");
	}
	
}