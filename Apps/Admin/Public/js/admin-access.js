$(document).ready(function(){
	$('input[id=1]').click(function(){
		var inputs = $(this).parents('.apps').find('input');
		inputs.prop('checked',$(this).prop('checked'));
//		inputs.attr('checked',$(this).prop('checked'));
	});
	
	$('input[id=2]').click(function(){
		var inputs = $(this).parents('.controls').find('input');
		inputs.prop('checked',$(this).prop('checked'));
//		inputs.attr('checked',$(this).prop('checked'));
	});
	
	$('#save').click(function() {
		if ($('#pid').val() == "") {
			// alert("请选择分类！");
			// document.getElementById("message").innerHTML = "名称不能为空！";
			$('#pid').focus();
			return false;
		}
		$('#saveFrom').submit();
	});
	
	$('#pid').change(function(){
		if($('#pid').val()==""){
			window.location.href= ajaxurl;
		}else{
			window.location.href= ajaxurl+"?pid="+$('#pid').val();
		}
		
//		$.ajax({
//			url: ajaxurl,
//			type: 'post',
//			data: "pid="+$('#pid').val(),
//			success: function(){
//				parent.location.reload();
//				window.location.reload();
//			},
//			dataType: 'html'
//		});
//		$.ajax(function(){
//			type:"POST",
//			dataType:"json",
//			url:"default.aspx",// 请求页面
//			data:"{id=1}",
//			complete:function(){location.href ="default.aspx"}// 跳转页面
//			});
		
	});
});

