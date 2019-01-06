<?php
namespace Home\Controller;
use Think\Controller;
class AllRecordController extends AuthController {
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
    	
    	//管理员权限
    	if(!$_GET['id']){
    		$id = $_SESSION [C ( 'USER_AUTH_KEY' )];
    	}else{
    		$id = $_GET['id'];
    	}
    	
    	//从数据库读取当月作息时间
    	import("Cls.HandleDay",APP_PATH);
    	$mdays = \HandleDay::getUserMothdays($cmoutn, $holidays, $workdays,$id);
    	
    	$applyworkdays = 0;
    	$applyholidays = 0;
    	$applyworkhours = 0;
    	$applyholihours = 0;
    	foreach ($mdays as $v){
    		if($v['worktime']){
    			$applyworkdays++;
    			$applyworkhours+=$v['worktime'];
    		}
    		if($v['holitime']){
    			$applyholidays++;
    			$applyholihours+=$v['holitime'];
    		}
    	}
    	$this->applyworkdays = $applyworkdays;
    	$this->applyholidays = $applyholidays;
    	$this->applyworkhours = $applyworkhours;
    	$this->applyholihours = $applyholihours;
    	
    	
    	$this->month = $cmoutn;
    	$this->sumdays = $sumdays;
    	$this->mdays = $mdays;
    	$this->workdays = $workdays;
    	$this->holidays = $holidays;
    	$userDB = M('dept')->join("INNER JOIN jh_user ON jh_user.dept_id = jh_dept.id");
    	$this->user=$userDB->where(array('jh_user.id'=>$id))->find();
    	$this->display();
    }
}