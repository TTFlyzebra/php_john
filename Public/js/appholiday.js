$(document).ready(function() {
	$('#holidayname').change(function(){
		if($('#holidayname').val()!=""){
			$.ajax({
				url:ajaxurl,
				type:"post",
				data:"holidayname=" + $('#holidayname').val(),
				dataType:'html',
				success: function(result) {
					$("#message").html("<font color='blue'><b>"+result+"</b></font>");
				}
			});
		}
		
	});
	
	
	$('#apply').click(function() {
		
		if ($('#holidayname').val() == "") {
			$('#holidayname').focus();
			return false;
		}
		
		if ($('#syear').val() == "") {
			$('#syear').focus();
			return false;
		}
		
		if ($('#smoth').val() == "") {
			$('#smoth').focus();
			return false;
		}
		
		if ($('#sday').val() == "") {
			$('#sday').focus();
			return false;
		}
		
		if ($('#shour').val() == "") {
			$('#shour').focus();
			return false;
		}
		
		if ($('#sminute').val() == "") {
			$('#sminute').focus();
			return false;
		}
		
		if ($('#eyear').val() == "") {
			$('#eyear').focus();
			return false;
		}
		
		if ($('#emoth').val() == "") {
			$('#emoth').focus();
			return false;
		}
		
		if ($('#eday').val() == "") {
			$('#eday').focus();
			return false;
		}
		
		if ($('#ehour').val() == "") {
			$('#ehour').focus();
			return false;
		}
		if ($('#eminute').val() == "") {
			$('#eminute').focus();
			return false;
		}
		if ($('#image1').val() == "") {
			$('#image1').focus();
			return false;
		}
		
		$('#form1').submit();
	});
});
