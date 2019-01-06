<?php
namespace Admin\Controller;
use Think\Controller;
class HolidayController extends AuthController {
    public function index(){
    	$this->holidays=M('holiday')->select();
    	$this->display();
    }
    
    public function add(){
    	if(!IS_POST) $this->error("页面不存在！");
    	if($_POST['holidayname']=="") $this->error("参数不正确！");
    	if(M('holiday')->add($_POST)){
    		$this->redirect("/Admin/Holiday");
    	}else{
    		$this->error("添加失败！");
    	}
    }
    
    public function del(){
    	if (! IS_POST) $this->error("页面不存在！");
    	$db = M('holiday');
    	if($db->where(array($_POST))->delete()){
    		$this->redirect("/Admin/Holiday");
    	}else{
    		$this->error($db->getError());
    	}
    }
}