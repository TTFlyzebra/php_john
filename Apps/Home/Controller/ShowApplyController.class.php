<?php
namespace Home\Controller;
use Think\Controller;
class ShowApplyController extends AuthController{
    public function index(){
    	if($_GET['page']==null){
    		$page = 1;
    	}else{
    		$page = $_GET['page'];
    	}
    	 
    	if($page<1){
    		$page=1;
    	}
    	
    	
    	$showitems = array();
    	import("Cls.HandleDay",APP_PATH);
    	//加班记录
//     	$applyworkDB = M('user');
//     	$applyworkDB->join("INNER JOIN jh_dept ON jh_user.dept_id = jh_dept.id");
//     	$applyworkDB->join("INNER JOIN jh_applyworkday ON jh_applyworkday.user_id = jh_user.id");
//     	$applyworkdays = $applyworkDB->where(array('user_id'=>$_SESSION [C ( 'USER_AUTH_KEY' )]))->select();
//     	foreach ($applyworkdays as $w){
//     		$showitems[] = \HandleDay::toShowItem($w);
//     	}
//     	//请假记录
//     	$applyholiDB = M('user');
//     	$applyholiDB->join("INNER JOIN jh_dept ON jh_user.dept_id = jh_dept.id");
//     	$applyholiDB->join("INNER JOIN jh_applyholiday ON jh_applyholiday.user_id = jh_user.id");
//     	$applyholidays = $applyholiDB->where(array('user_id'=>$_SESSION [C ( 'USER_AUTH_KEY' )]))->select();
//     	foreach ($applyholidays as $h){
//     		$showitems[] = \HandleDay::toShowItem($h);
//     	}
    	
    	$applyDB = M('user');
    	$applyDB->join("INNER JOIN jh_dept ON jh_user.dept_id = jh_dept.id");
    	$applyDB->join("INNER JOIN jh_apply ON jh_apply.user_id = jh_user.id");
    	$applys = $applyDB->where(array('user_id'=>$_SESSION [C ( 'USER_AUTH_KEY' )]))
    	->order(array('apptime'=>'desc'))
    	->limit(($page-1)*20,20)
    	->select();
    	foreach ($applys as $h){
    		$showitems[] = \HandleDay::toShowItem($h);
    	}

//     	//按申请时间排序
//     	$apptime = array();
//     	foreach ($showitems as $s) {
//     		$apptime[] = $s['apptime'];
//     	}
//     	array_multisort($apptime, SORT_DESC, $showitems);
    	
    	$this->page=$page;
    	$this->showitems = $showitems;
    	$this->display();
    }
    
    public function del(){
    	if (!IS_POST) $this->error("页面不存在！");
    	M('apply')->where(array('id'=>$_POST['id']))->delete();
    }
}