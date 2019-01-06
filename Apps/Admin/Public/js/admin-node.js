$(document).ready(function() {
	$('#add').click(function() {
		if ($('#node_name').val() == "") {
			// alert("请选择分类！");
			// document.getElementById("message").innerHTML = "名称不能为空！";
			$('#node_name').focus();
			return false;
		}
		if ($('#node_remark').val() == "") {
			// alert("请选择分类！");
			// document.getElementById("message").innerHTML = "名称不能为空！";
			$('#node_remark').focus();
			return false;
		}
		$('#addfrom').submit();
	});
});

function closeAddDlg(s1, s2, s3, s4, s5, s6) {
	$('.node-dlg-add').fadeOut(100);
	$('.node-mask').fadeOut(100);
}

function addDlg(s1, s2, s3) {
	$('.node-dlg-add').fadeIn(100);
	$('.node-mask').fadeIn(100);
	// 设置初始值
	document.getElementById("parent_name1").value = s1;
	document.getElementById("node_pid").value = s2;
	document.getElementById("node_level").value = s3;
}

function closeEditDlg(s1, s2, s3, s4, s5, s6) {
	$('.node-dlg-edit').fadeOut(100);
	$('.node-mask').fadeOut(100);
}

function editDlg(s1, s2, s3, s4, s5, s6,s7) {
	$('.node-dlg-edit').fadeIn(100);
	$('.node-mask').fadeIn(100);
	// 设置初始值
	document.getElementById("edit_id").value = s1;
	document.getElementById("edit_name").value = s2;
	document.getElementById("edit_remark").value = s3;
	document.getElementById("edit_status").value = s4;
	document.getElementById("edit_pid").value = s5;
	document.getElementById("edit_level").value = s6;
	document.getElementById("parent_name2").value = s7;
}

function delDlg(s1, s2, s3, s4, s5, s6) {
	$.ajax({
		url : delurl,
		type : 'post',
		data : "id=" + s1 + "&name=" + s2 + "&remark=" + s3 + "&status=" + s4
				+ "&pid=" + s5 + "&level=" + s6,
		success : function() {
			parent.location.reload();
			window.location.reload();
		},
		dataType : 'html'
	});
}