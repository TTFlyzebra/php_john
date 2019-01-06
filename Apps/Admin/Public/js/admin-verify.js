function verify(s1) {
	openDlg();
	var s2 = $("#role"+s1).val();
	$.ajax({
		url: ajaxurl,
		type: 'post',
		data: "user_id="+s1+"&role_id="+s2,
		success: function(result) {
			parent.location.reload();
			window.location.reload();
		},
		dataType: 'html'
	});
}

function openDlg() {
	$('.dlg-content').fadeIn(100);
	$('.dlg-mask').fadeIn(100);
}

function closeDlg() {
	$('.dlg-content').fadeOut(100);
	$('.dlg-mask').fadeOut(100);
}
