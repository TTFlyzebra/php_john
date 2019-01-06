function applyok(param1) {
	$.ajax({
		url : ajaxurlok,
		type : 'post',
		data : "id=" + param1,
		success : function() {
			parent.location.reload();
			window.location.reload();
		},
		dataType : 'html'
	});
}

function applyno(param1) {
	$.ajax({
		url : ajaxurlno,
		type : 'post',
		data : "id=" + param1,
		success : function() {
			parent.location.reload();
			window.location.reload();
		},
		dataType : 'html'
	});
}
