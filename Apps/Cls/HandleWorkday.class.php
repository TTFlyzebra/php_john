<?php
/**
 * 处理加班申请条目，最多最处理一天的时间，
 * 超过一天要分成多条申请
 * @author FlyZebra
 *
 */
class HandleWorkday {
	public $starttime;
	public $endtime;
	private $bstart;
	private $bend;
	private $time1 = 5; // 加班时间大于此变量，加班实际时间减1
	
	/**
	 * 初始化本类参数，各种时间等，自己瞄下代码更清楚吧*
	 * 
	 * @param array $post        	
	 */
	function __construct($post = null) {
		if ($post == null) {
			$post = $_POST;
		}
		$shour = $post ['shour'];
		$sminute = $post ['sminute'];
		$smonth = $post ['smonth'];
		$sday = $post ['sday'];
		$syear = $post ['syear'];
		
		$ehour = $post ['ehour'];
		$eminute = $post ['eminute'];
		$emonth = $smonth;
		$eday = $sday;
		$eyear = $syear;
		
		$starttime = mktime ( $shour, $sminute, 0, $smonth, $sday, $syear );
		$this->bstart = $syear == date ( 'Y', $starttime ) 
		&& $smonth == date ( 'n', $starttime ) 
		&& $sday == date ( 'j', $starttime ) 
		&& $shour == date ( 'G', $starttime ) 
		&& $sminute == date ( 'i', $starttime );
		$this->starttime = $starttime;
		
		// 考虑输入24点的申请情况//考虑到最后一月的最后一天最后一年的最后一天的情况
		if ($ehour == 24 && $eminute == 0) {
			$ehour = 0;
			$endtime = mktime ( $ehour, $eminute, 0, $emonth, $eday, $eyear ) + 3600 * 24;
			$et = $endtime - 3600 * 24;
		} else {
			$endtime = mktime ( $ehour, $eminute, 0, $emonth, $eday, $eyear );
			$et = $endtime;
		}
		$this->bend = $eyear == date ( 'Y', $et ) 
		&& $emonth == date ( 'n', $et ) 
		&& $eday == date ( 'j', $et ) 
		&& $ehour == date ( 'G', $et ) 
		&& $eminute == date ( 'i', $et );
		$this->endtime = $endtime;
	}
	
	/**
	 * 验证加班申请时间是否有效
	 * @param string $message 返回验证提示信息。
	 * @return 验证通过返回true，否则返回false。
	 */
	public function verifyTime(&$message=null) {
		if(!(
				$this->bstart //无效的开始时间
				&& $this->bend //无效的结束时间
				&& $this->starttime < $this->endtime//结束时间不能小于开始时间
				&& ($this->endtime - $this->starttime <= 24 * 3600)//加班时间不能大于24小时
				)){
			$message="加班时间填写有误！从".date("Y-n-j G:i",$this->starttime)."到".date("Y-n-j G:i",$this->endtime);
			return false;
		}
		
		//从数据库读取数据对比，加班有效时间是否有冲突		
// 		$sql = "user_id=".$_SESSION [C ( 'USER_AUTH_KEY' )]." AND ((starttime<=".$this->starttime." AND endtime>".$this->starttime.
// 		") OR (starttime<".$this->endtime." AND endtime>=".$this->endtime."))";
// 		=M('applyworkday')->where($sql)->find()
		
		$find =M('apply')->where("user_id=%d and ((starttime<=%d and endtime>%d) or (starttime<%d and endtime>=%d))",
				array($_SESSION [C ( 'USER_AUTH_KEY' )],$this->starttime,$this->starttime,$this->endtime,$this->endtime))->find();
		if($find){
			import("Cls.HandleDay",APP_PATH);
			$item = HandleDay::toShowItem($find);
			$message="时间重复！<font color='#000'>已存在[".$item['type']."]：".$item['describe']."。</font>";			
			return false;
		}
		return true;

	}
	
	/**
	 * 对应数据库'jh_applywork'，生成有效的加班时间数据项
	 * 
	 * @return unknown
	 */
	public function getApplywork($post = null, $id = null) {
		if ($post == null) {
			$post = $_POST;
		}
		if ($id == null) {
			$id = $_SESSION [C ( 'USER_AUTH_KEY' )];
		}
		$applyworkday ['user_id'] = $id;
		$applyworkday ['starttime'] = $this->starttime;
		$applyworkday ['endtime'] = $this->endtime;
		$applyworkday ['applytime'] = $this->getWorktime ();
		$applyworkday ['typename'] = 'applywork';
		$applyworkday ['workcontent'] = $post ['workcontent'];
		$applyworkday ['remark'] = $post ['remark'];
		$applyworkday ['appIP'] = get_client_ip ();
		$applyworkday ['apptime'] = time ();
		$applyworkday ['hand_id'] = self::setHandleUser($id,$this->starttime,$applyworkday ['applytime']);
		return $applyworkday;
	}
	
	/**
	 * 所有加班时间的计算处理都放在这里
	 * 目前只处理了加班时间大于5会自动减1的情况
	 * 以下为需要了解的情况
	 * 1.加班时间最大不超过多少小时
	 * 2.加班时间跨日的情况
	 * 获取有效加班时间
	 */
	private function getWorktime() {
		$starttime = $this->starttime;
		$endtime = $this->endtime;
		//获取当天公司上班时间安排
		import("Cls.HandleDay",APP_PATH);
		$mdays = \HandleDay::getMonthDays($starttime, $holidays, $workday);
		
		$cday=date( "j", $starttime)-1;
		//工作日加班
		if($mdays[$cday]['type']=="工作"){
			$shm=explode(":", $mdays[$cday]['stime']);
			$ehm=explode(":", $mdays[$cday]['etime']);
			$daystime = mktime ($shm[0], $shm[1], 0, date('n',$starttime), date('j',$starttime), date('Y',$starttime) );//当天的上班时间
			$dayetime = mktime ($ehm[0], $ehm[1], 0, date('n',$starttime), date('j',$starttime), date('Y',$starttime) );//当天的下班时间
			
			
			//填写的加班时间在正常上班的时间之间，把申请加班时间和下班时间设置成上下班时间
			if($starttime<$dayetime&&$starttime>=$daystime){
				$starttime=$dayetime;
			}
			if($endtime<=$dayetime&&$endtime>$daystime){
				$endtime=$daystime;
			}
			
			//加班时间在上班前
			if($endtime<=$daystime){
				$worktime = ($endtime-$starttime)/3600;
			}
			//加班时间在下班后
			if($starttime>=$dayetime){
				$worktime = ($endtime-$starttime)/3600;
			}
			//加班时间包括上午下午的情况
			if($starttime<$daystime&&$endtime>$dayetime){
				$worktime = ($endtime-$starttime)/3600-9.5;
			}
			
		}
		//休息日加班
		else{
			$worktime = ($endtime - $starttime) / 3600;
		}
		
		//加班时间超过5小时减去1小时
		if ($worktime >= C('WORK_SUMTIME')){
			$worktime = $worktime -C('WORK_SUBTIME');
		}
// 		echo date("Y-n-j G:i",$daystime)."----".date("Y-n-j G:i",$dayetime)."<br/>";
// 		echo date("Y-n-j G:i",$starttime)."----".date("Y-n-j G:i",$endtime);
		return $worktime;
	}
	
	/**
	 * 根据本月加班时间等设置由谁审批
	 */
	private function setHandleUser($id,$time,$ctime,$dept_id = null){
		//读取当月所有加班申请纪录
		$cmonth=mktime(0,0,0,date ( 'n', $time ),1, date ( 'Y', $time ));//当月开始的时间
		import("Cls.HandleDay",APP_PATH);
		$mdays = \HandleDay::getUserMothdays($cmonth, $holidays, $workdays,$id); 
		$sumtime = 0;
		foreach ($mdays as $v){
			if($v['worktime']){
				$sumtime+=$v['worktime'];
			}
		}
		$sumtime+=$ctime;
		
		if($dept_id==null){
			$dept_id = $_SESSION ['dept_id'];
		}
		if($sumtime<=C('HANDLE_WORK_MIN_TIME'.$dept_id)){
			$handID = C('HANDLE_WORK_MIN_USER'.$dept_id);
		}else{
			$handID = C('HANDLE_WORK_MAX_USER'.$dept_id);
		}
		return $handID;
	}
}
