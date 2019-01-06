var initializationTime = (new Date()).getTime();
function showLeftTime() {
	var now = new Date();
	var year = now.getFullYear();
	var month = now.getMonth()+1;
	var day = now.getDate();
	var hours = now.getHours();
	var minute = now.getMinutes();
	var minutes = minute<10?"0"+minute:minute;
	var second = now.getSeconds();
	var seconds = second<10?"0"+second:second;
	document.all.show.innerHTML = "" + year + "年" + month + "月" + day + "日 " + hours + ":" + minutes + ":" + seconds + "";
	// 一秒刷新一次显示时间
	var timeID = setTimeout(showLeftTime, 1000);
}