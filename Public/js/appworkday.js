$(document).ready(function() {
	$('#add').click(function() {
		if ($('#syear').val() == "") {
			$('#syear').focus();
			return false;
		}
		
		if ($('#smonth').val() == "") {
			$('#smonth').focus();
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
		
		if ($('#ehour').val() == "") {
			$('#ehour').focus();
			return false;
		}
		if ($('#eminute').val() == "") {
			$('#eminute').focus();
			return false;
		}
		if ($('#workcontent').val() == "") {
			$('#workcontent').focus();
			return false;
		}
		
		$('#from1').submit();
	});
});
