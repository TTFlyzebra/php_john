<?php
class User{
	
	/**
	 * 验证数据库中的帐号是否存在。
	 * 返回值:帐号不存在近回NULL否则返回帐号。
	 * @param string $tabla 数据表名
	 * @param string $userName 帐号
	 * @param string $name_id 帐号在数据表中的字段名
	 */
	public static function verifyName($tabla='user',$userName,$name_filed="loginname"){
		return M($tabla)->field($name_filed)->where(array($name_filed=>$userName))->find();
	}
	
	/**
	 * 验证数据库中的帐号和密码是否正确。
	 * 返回值:-1，验证帐号不存在;0,验证密码不正确;验证通过返回用户在数据表中的记录。
	 * @param string $tabla 数据表名
	 * @param string $userName 帐号
	 * @param string $userPswd 密码
	 * @param string $name_id 帐号在数据表中的字段名
	 * @param string $pwsd_id 密码在数据表中的字段名
	 * 
	 */
	public static function verifyUser($tabla='user',$userName,$userPswd,$name_filed="loginname",$pwsd_filed="loginword"){
		$data=M($tabla)->where(array($name_filed=>$userName))->find();
		if(!$data) return -1;
		if($data[$pwsd_filed]!=md5($userPswd)) return 0;
		return $data;
	}
	/**
	 * 向数据表$table中插入一条数据$data。
	 * 成功返回插入的数据，失败返回false。
	 * @param unknown $table
	 * @param unknown $data
	 */
	public static function insertUser($table='user',$data){
		//密码进行md5加密。
		$data['loginword'] = md5($data['loginword']);
		$ret=M($table)->add($data);
		if($ret){
			$data['id']=$ret;
			return $data;
		}else{
			return false;
		}
	}
	
	/**
	 * 插入帐号登陆信息。成功返回插入信息，失败返回false。
	 * @param unknown $table
	 * @param unknown $id
	 */
	public static function insertLog($table='user_log',$id,$user_id='user_id'){
		$data = array (
				$user_id => $id,
				'logintime' => time (),
				'loginIP'=>get_client_ip(),
		);
		if(M($table)->add($data)){
			return $data;
		}else{
			return false;
		}
	}
	
	/**
	 * 保存cookie
	 * @param unknown $data
	 * @param unknown $log
	 * @param number $time
	 */
	public static function saveCookie($data,$log,$time=3600){
		cookie ( 'loginname', $data ['loginname'], $time );
		cookie ( 'logincount', $data ['logincount'] );
		cookie ( 'id', $log ['user_id'], $time );
		cookie ( 'logintime', $log ['logintime'], $time );
		cookie ( 'loginIP', $log ['loginIP'], $time );
	}
	/**
	 * 保存session
	 * @param unknown $data
	 * @param unknown $log
	 */
	public static function saveSession($data,$log){
		session ( 'loginname', $data ['loginname'] );
		session ( 'logincount', $data ['logincount'] );
		session ( 'id', $log ['user_id'] );
		session ( 'logintime', $log ['logintime'] );
		session ( 'loginIP', $log ['loginIP'] );
		
	}
	
	
}