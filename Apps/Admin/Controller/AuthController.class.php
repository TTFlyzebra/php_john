<?php

namespace Admin\Controller;

use Think\Controller;
/**
 * Rbac认证基类，所有需要认证的控制器必须继续此类
 * @author Administrator
 *
 */
class AuthController extends Controller {
	public function _initialize() {
		$this->username = $_SESSION ['loginname'];
		// 不需要认证的控制器
		// $notModel = in_array(CONTROLLER_NAME,explode(',',C('NOT_AUTH_CONTROLLER')));
		// 不需要认证的方法
		
		// 没有登陆跳转到登陆界面
		if (! isset ( $_SESSION [C ( 'USER_AUTH_KEY' )] )) {
			$this->redirect ( "/Home/Login" );
		}
		
		// 没有访问权限返回原来目录
		if (C ( 'USER_AUTH_ON' ) && ! $notModel) {
			\Org\Util\Rbac::AccessDecision () || $this->error ( "没有访问权限" );
		}
		
		//找出登陆用户可以访问的所有路径确定哪些菜单可以显示
		$id = $_SESSION [C ( 'USER_AUTH_KEY' )] ;
		$user = M('role_user')->where(array('user_id'=>$id))->find();
		$data=D('RolePath')->relation(true)->field('id')->where(array('id'=>$user['role_id']))->find();
		$this->paths=path_merge($data['node']);
// 		如果是超级用户，显示所有菜单
		$this->super=$_SESSION [C ( 'ADMIN_AUTH_KEY' )]!=null;
		$this->display ( "Auth/index" );
	}
	
}