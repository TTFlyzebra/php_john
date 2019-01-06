<?php
namespace Home\Controller;
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
    	if ($count) die ( '名字重复，如存在同名员工，请添加标识以区分！' );
    }
    
    public function holiday(){
    	if (!IS_POST) $this->error("页面不存在！");
    	$data = $_POST;
    	$holiday = M('holiday')->where(array('holidayname'=>$_POST['holidayname']))->find();
    	echo $holiday['remark'];
    }
    
}