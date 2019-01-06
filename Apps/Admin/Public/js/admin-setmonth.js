$(document).ready(function() {
	$('input[id=ck]').click(function(){
		var stime = $(this).parents('tr').find('input[id=stime]');
		var etime = $(this).parents('tr').find('input[id=etime]');
		var type1 = $(this).parents('tr').find('td[id=type1]');
//		alert(stime.val());
		if($(this).prop('checked')){
			stime.val("");
			etime.val("");
			type1.html("休息");
			$(this).parents('tr').css('background','#989898');
			workday--;
			holiday++;
			$('#wokeholiday').html("工作日："+workday+"天&emsp;休息日："+holiday+"天");
		}else{
			stime.val(statime);
			etime.val(endtime);
			type1.html("上班");
			$(this).parents('tr').css('background','#fff');
			workday++;
			holiday--;
			$('#wokeholiday').html("工作日："+workday+"天&emsp;休息日："+holiday+"天");
		}
	});	
});

