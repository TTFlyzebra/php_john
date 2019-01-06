<?php
namespace Admin\Controller;
use Think\Controller;
class AjaxController extends Controller {
    public function index(){
    	echo  __ACTION__;
    }
    
    public function log(){
    	if (!IS_POST) $this->error("页面不存在！");
    	$data = $_POST;
    	if ($data['loginname'] == "") die("no loginname！");
    	$user = M ( 'user' )->where(array('loginname'=>$data['loginname']))->find();
    	if(!$user) die ( '用户未注册！' );
    	if ($data['loginword'] == "") die("no loginword！");
    	if ($user['loginword']!=md5($data['loginword'])) die("密码错误！");
    }
    
    public function reg(){
    	if (!IS_POST) $this->error("页面不存在！");
    	$data = $_POST;
    	if ($data['loginname'] == "") die("no loginname！");
    	$count = M ( 'user' )->where(array('loginname'=>$data['loginname']))->find();
    	if ($count) die ( '名字重复，如存在同名员工，请在名字中添加识别以区分（例如部门）' );
    }
    
}