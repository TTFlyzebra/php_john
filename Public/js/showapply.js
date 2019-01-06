function postdel(param1,param2) {
	$.ajax({
		url : ajaxurl,
		type : 'post',
		data : "id=" + param1+"&db="+param2,
		success : function() {
			parent.location.reload();
			window.location.reload();
		},
		dataType : 'html'
	});
}
