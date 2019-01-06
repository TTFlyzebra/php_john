<?php
namespace Home\Controller;
use Think\Controller;
class HandleApplyController extends AuthController{
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
    	
    	//根据审批设置确定要显示的记录
    	
    	$applyDB = M('user');
    	$applyDB->join("INNER JOIN jh_dept ON jh_user.dept_id = jh_dept.id");
    	$applyDB->join("INNER JOIN jh_apply ON jh_apply.user_id = jh_user.id");
    	$applys = $applyDB
    	->where(array('hand_id'=>$_SESSION [C ( 'USER_AUTH_KEY' )]))
    	->order(array('apptime'=>'desc'))
    	->limit(($page-1)*20,20)
    	->select();
    	foreach ($applys as $h){
    		$showitems[] = \HandleDay::toShowItem($h);
    	}
    	
    	
    	//...........
    	
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
    
    public function applyok(){
    	if (!IS_POST) $this->error("页面不存在！");
    	M('apply')->where(array('id'=>$_POST['id']))->data(array('status'=>1))->save();
    }
    
    public function applyno(){
    	if (!IS_POST) $this->error("页面不存在！");
   		M('apply')->where(array('id'=>$_POST['id']))->data(array('status'=>2))->save();
    }
}