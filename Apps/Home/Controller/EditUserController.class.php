<?php
namespace Home\Controller;
use Think\Controller;
class EditUserController extends AuthController{
    public function index(){
    	$this->depts=M('dept')->select();
    	$user = M('user')->field('id,dept_id,loginname,telephone,email')->where(array('id'=>$_SESSION [C ( 'USER_AUTH_KEY' )] ))->find(); 
  		$this->user = $user;
    	$this->display();
    }
    
    public function edit(){
    	if(!IS_POST) $this->error("页面不存在！");
		$upload = new \Think\Upload(); // 实例化上传类
		$upload->maxSize = 3145728; // 设置附件上传大小
		$upload->exts = array (	'jpg','png'); // 设置附件上传类型
		$upload->rootPath = './Uploads/';
		$upload->savePath = '/photo/'; // 设置附件上传目录
		// 接受上传的图像文件
		$info   =   $upload->uploadOne($_FILES['photo']);    
		if($info) {// 上传成功 获取上传文件信息         
			$data['photo']= '/Uploads'.$info ['savepath'].$info ['savename'];
			//图像压缩成300*400
			import ( "Fly.MyImage", "." );
			\MyImage::thumb ( "." . $data['photo'], 300, 400);
			session('photo',$data['photo']);
		}
		
		if($_POST['dept_id']){
			$data['dept_id']=$_POST['dept_id'];
			session('dept_id',$data['dept_id']);
			$dept = M('dept')->where(array('id'=>$_SESSION['dept_id']))->find();
			session('deptname',$dept['deptname']);
		}
		if($_POST['telephone']){
			$data['telephone']=$_POST['telephone'];
		}
		if($_POST['email']){
			$data['email']=$_POST['email'];
		}
		if($_POST['newpassword']){
			$data['loginword']=md5($_POST['newpassword']);
		}
		$data['id'] = $_SESSION [C ( 'USER_AUTH_KEY' )];
		dump($data);
		
		if(M('user')->where(array('id'=>$_SESSION [C ( 'USER_AUTH_KEY' )] ))->save($data)){
			$this->redirect("/Home/EditUser");
		}else{
			$this->error("修改失败！");
		}
    }
}