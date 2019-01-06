$(document).ready(function() {
	$('#add').click(function() {
		if ($('#role_name').val() == "") {
			// alert("请选择分类！");
			// document.getElementById("message").innerHTML = "名称不能为空！";
			$('#role_name').focus();
			return false;
		}
		if ($('#role_remark').val() == "") {
			// alert("请选择分类！");
			// document.getElementById("message").innerHTML = "名称不能为空！";
			$('#role_remark').focus();
			return false;
		}
		$('#addfrom').submit();
	});
});

function postdel(s1,s2,s3,s4,s5) {
	$.ajax({
		url: delurl,
		type: 'post',
		data: "id="+s1+"&name="+s2+"&pid="+s3+"&status="+s4+"&remark="+s5,
		success: function(){
			parent.location.reload();
			window.location.reload();
		},
		dataType: 'html'
	});
} 

function poststatus(s1,s2,s3,s4,s5) {
	$.ajax({
		url: statusurl,
		type: 'post',
		data: "id="+s1+"&name="+s2+"&pid="+s3+"&status="+s4+"&remark="+s5,
		success: function(){
			parent.location.reload();
			window.location.reload();
		},
		dataType: 'html'
	});
} 




