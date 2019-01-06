<?php
namespace Admin\Controller;
use Think\Controller;
class SetMonthController extends AuthController {
    public function index(){
    	//获取当前月的参数（星期，天数等）
    	if ($_GET ['month']!=null) {
    		$time = $_GET ['month'];
    	}else{
    		$time = time();
    	}
    	$sumdays = date("t",$time);
    	$year = date ( "Y",$time);
    	
    	$month = date ( "n",$time);    	
    	if($_GET['action']=="next"){
    		$month++;
    	}elseif($_GET['action']=="fore"){
    		$month--;
    	}    	
    	$cmoutn = mktime ( 0, 0, 0, $month, 1, $year );
    	
    	import("Cls.HandleDay",APP_PATH);
    	$mdays = \HandleDay::getMonthDays($cmoutn, $holidays, $workday);
    	$this->month = $cmoutn;
    	$this->sumdays = $sumdays;
    	$this->mdays = $mdays;
    	$this->workdays = $workday;
    	$this->holidays = $holidays;
    	$this->display();
    }
    
    public function saveset(){
    	if(!IS_POST) $this->error("页面不存在");
    	$stime = $_POST['stime'];
    	$etime = $_POST['etime'];
    	$mdays=array();
    	for($i=0;$i<count($stime);$i++){
    		$mdays[$i]['stime'] = $stime[$i];
    		$mdays[$i]['etime'] = $etime[$i];
    	}
    	$setmonth['month'] = $_POST['month'];
    	$setmonth['mdays'] = json_encode($mdays);
    	$db = M('setmonth');
    	$db->where(array('month'=>$setmonth['month']))->add($setmonth,'',$replace=true);
    	if($db){
    		$this->redirect("/Admin/SetMonth/index",array('month'=>$_POST['month']));
    	}else{
    		$this->error("保存数据失败");
    	}
    }
}