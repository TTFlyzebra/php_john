<?php
/**
 * 处理请假申请条目
 * @author FlyZebra
 *
 */
class HandleHoliday {
	public $starttime;
	public $endtime;
	private $bstart;
	private $bend;
	
	/**
	 * 初始化本类参数，各种时间等，自己看下代码更清楚吧*
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
		$emonth = $post ['emonth'];
		$eday = $post ['eday'];
		$eyear = $post ['eyear'];
		
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
	 * 验证加班时间的有效性
	 */
	public function verifyTime(&$message=null) {
		if (!(
				$this->bstart && // 无效的开始时间
				$this->bend && // 无效的结束时间
				$this->starttime < $this->endtime // 结束时间不能小于开始时间
			)){
			$message = "请假时间填写有误！从" . date ( "Y-n-j H:i", $this->starttime ) . "到" . date ( "Y-n-j H:i", $this->endtime );
			return false;
		}
		
		if($this->getHolidayDay()<=0){
			$message = "无效的请假时间！从" . date ( "Y-n-j H:i", $this->starttime ) . "到" . date ( "Y-n-j H:i", $this->endtime );
			return false;
		}
		
		//从数据库读取数据对比，加班有效时间是否有冲突
// 		$sql = "(starttime<=".$this->starttime." AND endtime>".$this->starttime.
// 		") OR (starttime<".$this->endtime." AND endtime>=".$this->endtime.")";
// 		$find=M('applyholiday')->where($sql)->find();
		$find =M('apply')->where("user_id=%d and ((starttime<=%d and endtime>%d) or (starttime<%d and endtime>=%d))",
				array($_SESSION [C ( 'USER_AUTH_KEY' )],$this->starttime,$this->starttime,$this->endtime,$this->endtime))->find();
		if($find){
			import("Cls.HandleDay",APP_PATH);
			$item = HandleDay::toShowItem($find);
			$message="时间重复！<font color='#000'>已存在申请：[".$item['type']."]".$item['describe']."。</font>";	
			return false;
		}
		//申请的时间超过该类假期规定天数，申请将无效
		
		
		
		return true;
	}
	
	/**
	 * 对应数据库'jh_applyholiday'，生成有效的加班时间数据项
	 * 
	 * @return unknown
	 */
	public function getApplyholiday($imageurl, $id = null, $post = null) {
		if ($id == null) {
			$id = $_SESSION [C ( 'USER_AUTH_KEY' )];
		}
		if ($post == null) {
			$post = $_POST;
		}
		$applyholiday ['user_id'] = $id;
		$applyholiday ['starttime'] = $this->starttime;
		$applyholiday ['endtime'] = $this->endtime;
		$applyholiday ['applytime'] = $this->getHolidayDay ();
		$applyholiday ['imgurl'] = $imageurl;
		$applyholiday ['typename'] = $post ['holidayname'];
		$applyholiday ['remark'] = $post ['remark'];
		$applyholiday ['appIP'] = get_client_ip ();
		$applyholiday ['apptime'] = time ();
		$applyholiday ['hand_id'] = self::setHandleUser($id,$this->starttime,$applyholiday ['applytime']*8);
		return $applyholiday;
	}
	
	/**
	 * 所有对请假时间的计算都放在这里处理,单位为天
	 * 获取有效请假时间，
	 * 先设置每月上班时间并存入数据库，取出对比，最后得出请假时间为多少小时
	 * 以下为需要确认的情况
	 * 1，需要了解情况：公司上班下班时间，休息日安排等等
	 * 2，需要明确请假具体单位为天还是为小时，请假时间最低时间为多少起，最高为多少小时
	 * 3，具体情况举例，若请假时间为10:00 - 23:00，如何计算
	 * 4，各种休假等情况（事假，病假）如何扣除上班时间，补偿上班时间。
	 * 5，请假时间包含休息日的情况如何处理。
	 * 这里有多种情况还需要处理，如请假可能连续的跨月跨年。
	 */
	private function getHolidayDay() {
		$stime = $this->starttime;
		$etime = $this->endtime;
		
		$syear = date ( 'Y', $stime );
		$smonth = date ( 'n', $stime );
		$sday = date ( 'j', $stime );
		$shour = date ( 'G', $stime );
		$sminute = date ( 'i', $stime );
		
		$eyear = date ( 'Y', $etime-1 );
		$emonth = date ( 'n', $etime-1 );
		$eday = date ( 'j', $etime-1 );
		$ehour = date ( 'G', $etime-1 );
		$eminute = date ( 'i', $etime-1 );
		
		$sth = C('WORK_START_HOUR');
		$stm = C('WORK_START_MIN');
		$eth = C('WORK_END_HOUR');
		$etm = C('WORK_END_MIN');
		
// 		echo date("Y-n-j G:i",$stime)."--".date("Y-n-j G:i",$etime).",<br>";
		
		$dayfstime = mktime ($sth, $stm, 0, $smonth, $sday, $syear );//请假第一天的上班时间
		$dayfetime = mktime ($eth, $etm, 0, $smonth, $sday, $syear );//请假第一天的下班时间
		$dayestime = mktime ($sth, $stm, 0, $emonth, $eday, $eyear );//请假最后一天的上班时间
		$dayeetime = mktime ($eth, $etm, 0, $emonth, $eday, $eyear );//请假最后一天的下班时间
		
		//开始时间小于当天上班时间的情况，有效时间为当天的上班时间
		if($stime<$dayfstime){
			$stime = $dayfstime;
		}
		
		//开始时间大于当天下班时间的情况，有效开始时间为下一天的上班时间
		elseif($stime>=$dayfetime){ 
			$stime = $dayfstime+24*3600;
		}
		
		//结束时间大于当天下班时间的情况，有效时间为当天下班时间
		if($etime>$dayeetime){
			$etime = $dayeetime;
		}
		
		//结束时间小于当天上班时间的情况，有效开始时间为上一天的下班时间
		elseif($etime<=$dayestime){
			$etime = $dayeetime-24*3600;
		}
		
// 		echo date("Y-n-j G:i",$stime)."--".date("Y-n-j G:i",$etime).",<br>";
		$ssday = mktime (0, 0, 0, date ( 'n', $stime ), date ( 'j', $stime ), date ( 'Y', $stime ) );
		$eeday = mktime (0, 0, 0, date ( 'n', $etime ), date ( 'j', $etime ), date ( 'Y', $etime ) );
		$holidayday = $eeday-$ssday;
		
		if($holidayday<0){
			$sumHoliday=0;
// 			echo "a--".$sumHoliday;die();
		}elseif($holidayday==0){
			$sumHoliday=(($etime-$stime)<=C('HOLIDAY_MIN_TIME')*3600)?0.5:1;
// 			echo "b--".$sumHoliday;die();
		}elseif($holidayday==24*3600){
			$fistHoliday = ((mktime($eth, $etm, 0, date("n",$stime), date("j",$stime), date("Y",$stime) )-$stime)<=C('HOLIDAY_MIN_TIME')*3600)?0.5:1;
			$endHoliday = (($etime-mktime ($sth, $stm, 0,  date("n",$etime), date("j",$etime), date("Y",$etime) ))<=C('HOLIDAY_MIN_TIME')*3600)?0.5:1;
			$sumHoliday = $fistHoliday+$endHoliday;
// 			echo "c--".$fistHoliday;
// 			echo "--".$endHoliday;
// 			echo "--".$sumHoliday;
// 			die();
		}else{
			$fistHoliday = ((mktime($eth, $etm, 0, date("n",$stime), date("j",$stime), date("Y",$stime) )-$stime)<=C('HOLIDAY_MIN_TIME')*3600)?0.5:1;
			$endHoliday = (($etime-mktime ($sth, $stm, 0,  date("n",$etime), date("j",$etime), date("Y",$etime) ))<=C('HOLIDAY_MIN_TIME')*3600)?0.5:1;
			$midHoliday = $holidayday/(24*3600)-1;
			$sumHoliday = $fistHoliday+$endHoliday+$midHoliday;
// 			echo "d--".$sumHoliday;die();
		}
		
		return $sumHoliday;
	}
	
	/**
	 * 根据请假时间设置由谁审批
	 */
	private function setHandleUser($id,$time,$ctime,$dept_id = null){
		//读取当月所有休假纪录
		$mstime=mktime(0,0,0,date ( 'n', $time ),1, date ( 'Y', $time ));//当月开始的时间
		import("Cls.HandleDay",APP_PATH);
		$mdays = \HandleDay::getUserMothdays($mstime, $holidays, $workdays,$id);
		$sumtime = 0;
		foreach ($mdays as $v){
			if($v['holitime']){
				$sumtime+=$v['holitime'];
			}
		}
		$sumtime+=$ctime;
		if($dept_id==null){
			$dept_id = $_SESSION ['dept_id'];
		}
		if($sumtime<=C('HANDLE_HOLI_MIN_TIME'.$dept_id)){
			$handID = C('HANDLE_HOLI_MIN_USER'.$dept_id);
		}else{
			$handID = C('HANDLE_HOLI_MAX_USER'.$dept_id);
		}
		return $handID;
	}
}
