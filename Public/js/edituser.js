$(document).ready(function() {
	$('#eidt').click(function() {
		if ($('#dept_id').val() == "") {
			$('#message').html("<font color='red'><b>请选择部门</b></font>");
			$('#dept_id').focus();
			return false;
		}

		if ($('#password').val() == "") {
			$('#message').html("<font color='red'><b>请输入密码</b></font>");
			$('#password').focus();
			return false;
		}
		
		if ($('#newpassword').val() != $('#newpassword1').val()) {
			$('#message').html("<font color='red'><b>两次输入密码不一致</b></font>");
			$('#newpassword').focus();
			return false;
		}
		
		// 判断用户名密码是否正确
		
		var password = hex_md5(document.getElementById("password").value);
		
		$.ajax({
			url:ajaxurl,
			type:"post",
			data:"loginname=" + loginname+"&loginword="+password,
			dataType:'html',
			success: function(result) {
				if (result.length > 2) {
					$("#message").html("<font color='red'><b>"+result+"</b></font>");
					return false;
				} else {
					document.getElementById("password").value = password;
					if ($('#newpassword').val() != "") {
						newpassword=hex_md5(document.getElementById("newpassword").value);
						document.getElementById("newpassword").value = newpassword;
					}
					$('#form1').submit();
				}
			}
		});
	});
});
