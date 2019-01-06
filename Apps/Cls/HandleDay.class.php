<?php
/**
 * 处理各种加班时间
 * @author FlyZebra
 *
 */
class HandleDay {
	
	public static $status = array('等待批准','已同意','不同意');
	public static $type = array('加班申请','休假申请','调休申请');
	public static $weeks  = array('日','一','二','三','四','五','六');
	/**
	 * 
	 * 转换加班请假等记录，方便以统一的形式在表格中输出，并且能过滤不需要显示的字段
	 * @param unknown $sqldata
	 */
	public static function toShowItem($sqldata){
		$showitem['id'] = $sqldata['id'];
		$showitem['user_id'] = $sqldata['user_id'];
		$showitem['dept_id'] = $sqldata['dept_id'];
		$handuser = M('user')->where(array('id'=>$sqldata['hand_id']))->find();
		if($handuser){
			$showitem['handuser'] = $handuser['loginname'];
		}
		$showitem['deptname'] = $sqldata['deptname'];
		$showitem['loginname'] = $sqldata['loginname'];
		$showitem['status'] = $sqldata['status'];
		$showitem['tstatus'] = self::$status[$sqldata['status']];
// 		$showitem['apptime'] = date("Y-n-j G:i:s",$sqldata['apptime']);
		$showitem['apptime'] = $sqldata['apptime'];
		
		//加班申请转换
		if($sqldata['typename']=='applywork'){
			$showitem['type'] = self::$type[0];
			$showitem['describe'] = date("n月j日H:i",$sqldata['starttime'])."到".date("n月j日H:i",$sqldata['endtime'])."&emsp;时间".$sqldata['applytime']."小时&emsp;".$sqldata['workcontent'];
		}else{
			$showitem['type'] = self::$type[1];
			$showitem['describe'] = date("n月j日H:i",$sqldata['starttime'])."到".date("n月j日H:i",$sqldata['endtime'])."&emsp;时间".$sqldata['applytime']."天&emsp;".$sqldata['typename']
			."&emsp;<a target=_blank href=".U($sqldata['imgurl']).">查看附件</a>";
		}
		
		return $showitem;
	}
	
	/**
	 * 获取公司设置的指定月份的作息时间  返回mdays[]:day = 月,week = 星期,stime=上班时间(H:m)，etime = 下班时间(H:m), type = 工作/休息
	 * @param unknown $time 传入的时间，从该时间获取具体月份
	 * @param unknown $holidays 本月休息天数
	 * @param unknown $workdays 本月工作天数
	 * @return
	 */
	public static function getMonthDays($time,&$holidays,&$workdays){ 
		
		$year = date ( "Y",$time);
		$month = date ( "n",$time); 
		$setmouth=M('setmonth')->where(array('month'=>$time))->find();
		//已经设置了作息时间
		if($setmouth){
			$mdays = json_decode($setmouth['mdays'],true);
			for($i=0;$i<count($mdays);$i++){
				$now_time = mktime ( 0, 0, 0, $month, $i+1, $year );
				$mdays[$i]['day'] = $i+1;
				$mdays[$i]['week'] = self::$weeks [date ( "w", $now_time )];
				if ($mdays[$i]['stime'] == "") {
					$mdays[$i] ['type'] = "休息";
					$holidays ++;
				} else {
					$mdays[$i] ['type'] = "工作";
					$workdays ++;
				}
			}
			 
		}
		//没有设置作息时间
		else {
			$mdays = array();
			// 当月工作天数统计
			for($i = 1; $i <= $sumdays = date("t",$time); $i++) {
				$now_time = mktime ( 0, 0, 0, $month, $i, $year );
				$date ['day'] = date ( "d", $now_time );
				$date ['week'] = self::$weeks [date ( "w", $now_time )];
				if ($date ['week'] == "日" || $date ['week'] == "六") {
					$date ['type'] = "休息";
					$date ['stime'] = "";
					$date ['etime'] = "";
					$holidays ++;
				} else {
					$date ['type'] = "工作";
					$date ['stime'] = C('WORK_START_HOUR').":".C('WORK_START_MIN');
					$date ['etime'] = C('WORK_END_HOUR').":".C('WORK_END_MIN');
					$workdays ++;
				}
				$mdays [] = $date;
			}
		}
		return $mdays;
	}
	
	/**
	 * 获取当月的作息情况 返回mdays[]:day = 月,week = 星期,stime=上班时间(H:m)，etime = 下班时间(H:m), type = 工作/休息
	 * @param unknown $userid
	 * @param unknown $time
	 */
	public static function getUserMothdays($time=null,&$holidays=null,&$workdays=null,$userid=null){
		if($time==null){//如果没有传入时间，默认为当前时间
			$time = time();
		}
		if($userid==null){//如果没有传入用户ID，默认为当前登陆用户ID
			$userid = $_SESSION [C ( 'USER_AUTH_KEY' )];
		}
		$mdays = self::getMonthDays($time, $holidays, $workdays); 
		$mstime=mktime(0,0,0,date ( 'n', $time ),1, date ( 'Y', $time ));//当月开始的时间
		$metime=mktime(24,0,0,date ( 'n', $time ),date ( "t", $time ), date ( 'Y', $time ));//当月结束的时间
		//根据本月加班时间设置当月时间
		//获取当月所有加班记录，并将每条加班记录的情况加入到作息时间表，不同意的申请不处理
		$applyworkdays = M('apply')->where("typename='applywork' AND status!=2 AND user_id=%d AND starttime>=%d AND endtime<=%d",array($userid,$mstime,$metime))->select();
		foreach ($applyworkdays as $v) { 
			$wshtime = date ( 'G', $v ['starttime'] );
			$wsmtime = date ( 'i', $v ['starttime'] );
			$wehtime = date ( 'G', $v ['endtime'] );  
			$wemtime = date ( 'i', $v ['endtime'] );	
			//工作日加班时间显示处理
			$cday=date( "j", $v['starttime'])-1;
			if ($mdays[$cday] ['type'] == '工作') {
				$shm=explode(":", $mdays[$cday]['stime']);				
				$ehm=explode(":", $mdays[$cday]['etime']);
				if($wshtime>$shm[0]){
					$wshtime = $shm[0];
					$wsmtime = $shm[1];
				}else if($wshtime==$shm[0]){
					if($wsmtime>$shm[1]){
						$wsmtime=$shm[1];
					}
				}
				if ($wehtime == 0 && $wemtime == 0) {
					$wehtime = 24;
				} elseif ($wehtime < $ehm[0]) {
					$wehtime = $ehm[0];
					$wemtime = $ehm[1];
				} elseif ($wehtime == $ehm[0]) {
					if ($wemtime < $ehm[1]) {
						$wemtime = $ehm[1];
					}
				}
				
			}else{
				//结束时间为0点设置显示为24:00
				if ($wehtime == 0 && $wemtime == 0) {
					$wehtime = 24;
				}
			}
    		$mdays[$cday]['stime'] = $wshtime.":".$wsmtime;
    		$mdays[$cday]['etime'] = $wehtime.":".$wemtime;
    		$mdays[$cday]['worktime'] = $mdays[$cday]['worktime']+$v['applytime'];
    		if($mdays[$cday]['workcontent']!=null){
    			$mdays[$cday]['workcontent']=$mdays[$cday]['workcontent']."，".$v['workcontent'];
    		}else{
    			$mdays[$cday]['workcontent']=$v['workcontent'];
    		}
    		$mdays[$cday]['tstatus']= self::$status[$v['status']];
    	}
		
		
		//根据本月休假时间设置当月时间
    	//获取当月所有加班记录，并将每条加班记录的情况加入到作息时间表，有跨月的情况发生
    	$holidays = M('apply')->where("typename!='applywork' AND status!=2 AND user_id=%d AND ((starttime>=%d AND starttime<%d) OR (endtime>=%d AND endtime<%d) OR (starttime<=%d AND endtime>%d))",array($userid,$mstime,$metime,$mstime,$metime,$mstime,$metime))->select();
    	foreach ($holidays as $v) {
    		//处理跨月的记录,处理方法为把开始结束时间为本月开始上班下班时间
    		$stime = $v['starttime'];
    		$etime = $v['endtime'];
    		$cday=date( "j", $mstime)-1;
    		$shm=explode(":", $mdays[$cday]['stime']);
    		$ehm=explode(":", $mdays[$cday]['etime']);
    		$shm[0]=$shm[0]?$shm[0]:0;
    		$shm[1]=$shm[1]?$shm[1]:0;
    		$ehm[0]=$ehm[0]?$ehm[0]:0;
    		$ehm[1]=$ehm[1]?$ehm[1]:0;
    		if($stime<$mstime){
    			$cday=date( "j", $mstime)-1;
    			$stime = mktime($shm[0],$shm[1],0,date ( 'n', $mstime ),1, 2016);
    		}
    		if($etime>$metime){
    			$etime = mktime($ehm[0],$ehm[1],0,date ( 'n', $mstime ),date ( "t", $mstime ), date ( 'Y', $mstime ));
    		}
    		
    		$cday=date( "j", $stime)-1;
    		
    		$syear = date ( 'Y', $stime );
    		$smonth = date ( 'n', $stime );
    		$sday = date ( 'j', $stime );
    		
    		$eyear = date ( 'Y', $etime-1 );
    		$emonth = date ( 'n', $etime-1 );
    		$eday = date ( 'j', $etime-1 );
    		
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
    		}elseif($holidayday==0){
    			$sumHoliday=(($etime-$stime)<=C('HOLIDAY_MIN_TIME')*3600)?0.5:1;
    			self::setMdays($cday, $mdays,$sumHoliday,$v,$stime,$etime);
    		}elseif($holidayday==24*3600){
    			$fistHoliday = ((mktime($eth, $etm, 0, date("n",$stime), date("j",$stime), date("Y",$stime) )-$stime)<=C('HOLIDAY_MIN_TIME')*3600)?0.5:1;
    			$endHoliday = (($etime-mktime ($sth, $stm, 0,  date("n",$etime), date("j",$etime), date("Y",$etime) ))<=C('HOLIDAY_MIN_TIME')*3600)?0.5:1;
    			self::setMdays($cday, $mdays,$fistHoliday,$v,$stime,$etime);
    			self::setMdays($cday+1, $mdays,$endHoliday,$v,$stime,$etime);
    		}else{
    			$fistHoliday = ((mktime($eth, $etm, 0, date("n",$stime), date("j",$stime), date("Y",$stime) )-$stime)<=C('HOLIDAY_MIN_TIME')*3600)?0.5:1;
    			$endHoliday = (($etime-mktime ($sth, $stm, 0,  date("n",$etime), date("j",$etime), date("Y",$etime) ))<=C('HOLIDAY_MIN_TIME')*3600)?0.5:1;
    			$midHoliday = $holidayday/(24*3600)-1;
    			self::setMdays($cday, $mdays,$fistHoliday,$v,$stime,$etime);
    			for($i=1;$i<=$midHoliday;$i++){
    				self::setMdays($cday+$i, $mdays,1,$v,$stime,$etime);
    			}
    			self::setMdays($cday+$i, $mdays,$endHoliday,$v,$stime,$etime);
    		}
    		
    	}
		//根据本月调休时间设置当月时间
		
		return $mdays;
		
	}
	
	/**
	 * 设置休假显示记录
	 * @param unknown $cday
	 * @param unknown $mdays
	 * @param unknown $sumHoliday
	 * @param unknown $v
	 */
	public static function setMdays($cday,&$mdays,$sumHoliday,$v,$stime,$etime){
		$mdays[$cday]['holidayname'] = $v['typename'];
		$mdays[$cday]['tstatus']= self::$status[$v['status']];
		if($mdays[$cday]['type']=="工作"){
			$mdays[$cday]['holitime'] += $sumHoliday*8;
			if($mdays[$cday]['holitime']==8){
				$mdays[$cday]['stime'] ="";
				$mdays[$cday]['etime'] ="";
			}elseif ($mdays[$cday]['holitime']==4){
				if($mdays[$cday]['stime'] ==date("G:i",$stime)){
					$mdays[$cday]['stime'] =date("G:i",$etime);
				}else{
					$mdays[$cday]['etime'] =date("G:i",$stime);
				}
				
			}
			
		}
		
		
	}
	
}
 