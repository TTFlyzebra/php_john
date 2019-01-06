<?php

namespace Home\Controller;

use Think\Controller;

class RegisterController extends Controller {
	public function index() {
		$this->depts=M('dept')->select();
		$this->display ();
	}
	
	public function register(){
		if(!IS_POST) $this->error ("页面不存在！");
		$data = $_POST;
		$upload = new \Think\Upload(); // 实例化上传类
		$upload->maxSize = 3145728; // 设置附件上传大小
		$upload->exts = array (	'jpg','png'); // 设置附件上传类型
		$upload->rootPath = './Uploads/';
		$upload->savePath = '/photo/'; // 设置附件上传目录
		// 接受上传的图像文件
		$info   =   $upload->uploadOne($_FILES['photo']);    
		if(!$info) {// 上传错误提示错误信息       
			$this->error($upload->getError());   
		}else{// 上传成功 获取上传文件信息         
			$data['photo']= '/Uploads'.$info ['savepath'].$info ['savename'];
			//图像压缩成300*400
			import ( "Fly.MyImage", "." );
			\MyImage::thumb ( "." . $data['photo'], 300, 400);
		}
		$data['loginword']=md5($data['loginword']);//密码MD5加密
		$data['regtime']=time();
		$db = M('user');
		if($userid=$db->add($data)){
			$roledb = M('role_user');
			$roledb->where(array('user_id'=>$userid))->delete();
			if(C('USER_ROLE_ID')){
				$roledb->add(array('role_id'=>C('USER_ROLE_ID'),'user_id'=>$userid));
			}
			$this->success("注册成功，请登陆。",U("/Home/Login"));
		}else{
			$this->error("注册失败！");
		}
	}
}