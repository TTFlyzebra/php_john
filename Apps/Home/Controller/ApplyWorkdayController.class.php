<?php
namespace Home\Controller;
use Think\Controller;
class ApplyWorkdayController extends AuthController {
    public function index(){
    	
    	//初始化加班页面填写的时间
    	$today = array(
    			'year' => date('Y'),
    			'month' => date('n'),
    			'day' => date('j'),
    			'hour' => date('H'),
    			'minute' => date('i'),
    	);
    	$this->today = $today;
    	
    	$this->display();
    }
    
    public function add(){
    	if(!IS_POST) $this->error("页面不存在!");
    	
    	import("Cls.HandleWorkday",APP_PATH);
    	$handleWorkday = new \HandleWorkday();
    	
    	if(!$handleWorkday->verifyTime($message)) 
    		$this->error($message,'',10);
    	
    	//初始化加班申请条
    	$applywork=$handleWorkday->getApplywork();
    	if(M('apply')->add($applywork)){
    		$this->redirect("/Home/ShowApply");
    	}else{
    		$this->error("操作失败，请联系系统管理员！");
    	}
    }
}