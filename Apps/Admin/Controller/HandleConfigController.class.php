<?php
namespace Admin\Controller;
use Think\Controller;
class HandleConfigController extends AuthController {
    public function index(){
    	$this->dept=M('dept')->select();
    	$userAllDB = M('user');
    	$userAllDB->join("INNER JOIN jh_dept ON jh_user.dept_id = jh_dept.id");
    	$userAllDB->join("INNER JOIN jh_role_user ON jh_user.id = jh_role_user.user_id");
    	$userAllDB->join("INNER JOIN jh_role ON jh_role.id = jh_role_user.role_id");
    	$user=$userAllDB->field('jh_user.id as user_id,loginname,deptname,jh_role.remark as role_remark,jh_role.id as role_id')
    	->where(array('role_id'=>C('HAND_ROLE_ID')))
    	->select();
//     	$userArr=D('UserRole')->field('loginword',true)->relation('role')->where(array('role_id'=>C('HAND_ROLE_ID')))->select();
//     	foreach ( $userArr as $user ) {
//     		$user['role_id'] = $user['role'][0]['id'];
//     		$user['role_remark'] = $user['role'][0]['remark'];
//     		unset($user['role']);
//     		$data[]=$user;
//     	}
    	$this->user=$user;
    	$this->display();
    }
    
    public function save(){
    	if(!IS_POST) $this->error("页面不存在。");
//     	dump($_POST);
//     	dump(F('flyzebra',$_POST,CONF_PATH));
//     	F('webset', $_POST, APP_PATH.MODULE_NAME.'/Conf/');
// 		echo CONF_PATH;
// 		die();
    	if(write_config_arr($_POST, CONF_PATH."/handset.php")){
    		$this->success("修改成功！",U('/Admin/HandleConfig'));
    	}else{
    		$this->error("修改失败！");
    	}
    }
}