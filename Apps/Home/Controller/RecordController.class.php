<?php
namespace Home\Controller;
use Think\Controller;
class RecordController extends AuthController {
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
    	
    	//从数据库读取当月作息时间
    	import("Cls.HandleDay",APP_PATH);
    	$mdays = \HandleDay::getUserMothdays($cmoutn, $holidays, $workdays,$id); 
    	
    	//设置加班记录的显示
    	
//     	//加班记录
//     	$applyworkDB = M('applyworkday');
//     	$applyworkDB->join("INNER JOIN jh_user ON jh_applyworkday.user_id = jh_user.id");
//     	$applyworkdays = $applyworkDB->where(array('user_id'=>$_SESSION [C ( 'USER_AUTH_KEY' )]))->select();
//     	foreach ($applyworkdays as $w){
//     	}
//     	//请假记录
//     	$applyholiDB = M('applyholiday');
//     	$applyholiDB->join("INNER JOIN jh_user ON jh_applyholiday.user_id = jh_user.id");
//     	$applyholidays = $applyholiDB->where(array('user_id'=>$_SESSION [C ( 'USER_AUTH_KEY' )]))->select();
//     	foreach ($applyholidays as $h){
//     	}
//     	//设置休假记录的显示
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
    	$this->display();
    }
}