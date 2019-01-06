<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends AuthController {
    public function index(){
    	$this->display();
    }
}