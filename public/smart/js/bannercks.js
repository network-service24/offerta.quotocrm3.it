function checkcks(cks){
	if (document.cookie.length > 0){
		var cookie = document.cookie.indexOf(cks + "=");
		if (cookie != -1){
			cookie = cookie + cks.length + 1;
			var end = document.cookie.indexOf(";",cookie);
			if (end == -1) end = document.cookie.length;
			return unescape(document.cookie.substring(cookie,end));
		}else{
			return "";
		}
	}
	return "";
}

function scrivi(cks,valore,durata){
	var scadenza = new Date();
	var attuale = new Date();
	scadenza.setTime(attuale.getTime() + (parseInt(durata) * 86400000));
	document.cookie = cks + '=' + escape(valore) + '; expires=' + scadenza.toGMTString() + '; path=/';
}

$(document).ready(function(){
	var ncks = checkcks('bannercks');
	if (ncks !== 'ckswritten') {
		setTimeout(function(){
			$("#bnrcks").css('max-height', '1000px');
		}, 1000);
	}

	$(document).scroll(function(){
		scrivi('bannercks','ckswritten',365);
		$("#bnrcks").css('max-height', 0);
	});
	
	$('.okcks').click(function(event) {
		scrivi('bannercks','ckswritten',365);
		$("#bnrcks").css('max-height', 0);
	});

	$('.privacybtn').click(function(event) {
		$('.privacy').css('left', 0);
	});
	$('.privacy #close').click(function(event) {
		$('.privacy').css('left', '-120%');
	});

	$('.npcks').click(function(event) {
		$('.cookies').css('left', 0);
	});
	$('.cookies #close').click(function(event) {
		$('.cookies').css('left', '-120%');
	});
});