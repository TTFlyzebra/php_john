<?php
namespace Admin\Controller;
use Think\Controller;
class DeptController extends AuthController {
    public function index(){
    	$this->depts=M('dept')->select();
    	$this->display();
    }
    
    public function add(){
    	if(!IS_POST) $this->error("页面不存在！");
    	if($_POST['deptname']=="") $this->error("参数不正确！");
    	if(M('dept')->add($_POST)){
    		$this->redirect("/Admin/Dept");
    	}else{
    		$this->error("添加失败！");
    	}
    }
    
    public function del(){
    	if (! IS_POST) $this->error("页面不存在！");
    	$db = M('dept');
    	if($db->where(array($_POST))->delete()){
    		$this->redirect("/Admin/Dept");
    	}else{
    		$this->error($db->getError());
    	}
    }
}