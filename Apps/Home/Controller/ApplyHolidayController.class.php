<?php
namespace Home\Controller;
use Think\Controller;
class ApplyHolidayController extends AuthController {
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
    	$this->holidays = M('holiday')->select();
    	
    	
    	$this->display();
    }
    
    public function add(){
	    if(!IS_POST) $this->error("页面不存在!");
	    import("Cls.HandleHoliday",APP_PATH);
	    $handleHoliday = new \HandleHoliday();

	    if(!$handleHoliday->verifyTime($message))
    	$this->error($message,'',10);
        	
    	$upload = new \Think\Upload(); // 实例化上传类
    	$upload->maxSize = 3145728; // 设置附件上传大小
    	$upload->exts = array (	'jpg','png'); // 设置附件上传类型
    	$upload->rootPath = './Uploads/';
    	$upload->savePath = '/holiday/'; // 设置附件上传目录
    	// 接受上传的图像文件
    	$info   =   $upload->uploadOne($_FILES['image1']);
    	if(!$info) {// 上传错误提示错误信息
    		$this->error($upload->getError());
    	}else{// 上传成功 获取上传文件信息
    		$imgurl= '/Uploads'.$info ['savepath'].$info ['savename'];
    	}
    	
    	//初始化请假申请条
    	$applyholiday=$handleHoliday->getApplyholiday($imgurl);
    	
    	if(M('apply')->add($applyholiday)){
    		$this->redirect("/Home/ShowApply");
    	}else{
    		$this->error("操作失败，请联系系统管理员！");
    	}
    }
}