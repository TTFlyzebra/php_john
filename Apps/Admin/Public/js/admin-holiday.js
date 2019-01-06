$(document).ready(function() {
	$('#add').click(function() {
		if ($('#holidayname').val() == "") {
			// alert("请选择分类！");
			// document.getElementById("message").innerHTML = "名称不能为空！";
			$('#holidayname').focus();
			return false;
		}
		
		if ($('#applydays').val() == "") {
			// alert("请选择分类！");
			// document.getElementById("message").innerHTML = "名称不能为空！";
			$('#applydays').focus();
			return false;
		}
		$('#addfrom').submit();
	});
});

function postdel(param1, param2, param3, parma4) {
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : "id=" + param1,
		success : function() {
			parent.location.reload();
			window.location.reload();
		},
		dataType : 'html'
	});
}
