<?php
namespace Admin\Controller;
use Think\Controller;
class ConfigController extends AuthController {
    public function index(){
    	$this->role=M('role')->select();
    	$this->display();
    }
    
    public function save(){
    	if(!IS_POST) $this->error("页面不存在。");
//     	dump($_POST);
//     	dump(F('flyzebra',$_POST,CONF_PATH));
//     	F('webset', $_POST, APP_PATH.MODULE_NAME.'/Conf/');
// 		echo CONF_PATH;
// 		die();
    	if(write_config_arr($_POST, CONF_PATH."/webset.php")){
    		$this->success("修改成功！",U('/Admin/Config'));
    	}else{
    		$this->error("修改失败！");
    	}
    }
}