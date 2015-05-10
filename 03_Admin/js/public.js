jQuery.event.add(window, "load", function(){
 // IE CSS3反映用
	if (window.PIE) {
		// 通常グラデーション
  jQuery("header,#topicPath,#topicPath,.pageList_link strong,.pageList_link a,html.login body #loginBox h1,footer,a.themeSub, .themeSub,a.themeNormal, .themeNormal,#loginBox").each(function(){PIE.attach(this);});
		
		function pieLoad(){
			jQuery("#jquery-ui-dialog .themeNormal,#jquery-ui-dialog .pageList_link a,#jquery-ui-dialog .pageList_link strong").each(function(){PIE.attach(this);});
			return false;
		}
		
		jQuery(document).on("click",".jquery-ui-opener,.jquery-ui-opener-p,.jquery-ui-opener-c,.jquery-ui-opener-t,.jquery-ui-opener-t-set",function(){
			setInterval(pieLoad,300);
		});
		// hover時グラデーション
		//jQuery("#side section li a").hover(function(){PIE.attach(this);
			//jQuery(this).each(function(){});
		//});
 }
	
	// side navi
	// stayURL();
	
 // pagetop
	var topBtn=$('#page-top');
	topBtn.hide();
	// スクロールが100に達したらボタン表示
	$(window).scroll(function(){
		if($(this).scrollTop()>100){
			topBtn.fadeIn();
		}else{
			topBtn.fadeOut();
		}
	});
	// スクロールしてトップ
	topBtn.click(function(){
		$('body,html').animate({
			scrollTop:0
		},500);
		return false;
	});
});

/* サイドメニューで表示中のリンクをアクティブ化
function stayURL(){
	stayPath=location.pathname;
	jQuery("#side section ul li").each(function(){
		var stayClass=jQuery(this).attr("class");
		if(stayPath.match(stayClass)){
			jQuery(this).children("a").addClass("stay");
	 }else{
	 }
	});
}*/

//----------ポップアップ
function MM_openBrWindow(theURL,winName,features) { //v2.0
  window.open(theURL,winName,features);
}
function openwinAllNo(num,winw,winh){
  	wl=((screen.availWidth) /10)+30;
	wt=((screen.availHeight) /6)+30;
	sw3=window.open(num,"_blank","resizable=no,menubar=no,directories=no,status=no,location=no,scrollbars=no,width="+winw+",height="+winh+",left="+wl+",top="+wt+"");
      	if ((navigator.appName.charAt(0) == "N" && navigator.appVersion.charAt(0) >= 3) || (navigator.appName.charAt(0) == "M" && navigator.appVersion.charAt(0) >= 4)){
        sw3.focus();
      }
}
function openwinAllYes(num,winw,winh){
  	wl=((screen.availWidth) /10)+30;
	wt=((screen.availHeight) /6)+30;
	sw3=window.open(num,"_blank","resizable=yes,menubar=yes,directories=yes,status=yes,location=yes,scrollbars=yes,width="+winw+",height="+winh+",left="+wl+",top="+wt+"");
      	if ((navigator.appName.charAt(0) == "N" && navigator.appVersion.charAt(0) >= 3) || (navigator.appName.charAt(0) == "M" && navigator.appVersion.charAt(0) >= 4)){
        sw3.focus();
      }
}

//----------ロールオーバー
function initRollovers() {
	if (!document.getElementById) return

	var aPreLoad = new Array();
	var sTempSrc;
	var aImages = document.getElementsByTagName('img');
//	var aInputs = document.getElementsByTagName('input');

	for (var i = 0; i < aImages.length; i++) {
		if (aImages[i].className == 'btnover') {
			var src = aImages[i].getAttribute('src');
			var ftype = src.substring(src.lastIndexOf('.'), src.length);
			var hsrc = src.replace(ftype, '_on'+ftype);

			aImages[i].setAttribute('hsrc', hsrc);

			aPreLoad[i] = new Image();
			aPreLoad[i].src = hsrc;

			aImages[i].onmouseover = function() {
				sTempSrc = this.getAttribute('src');
				this.setAttribute('src', this.getAttribute('hsrc'));
			}

			aImages[i].onmouseout = function() {
				if (!sTempSrc) sTempSrc = this.getAttribute('src').replace('_on'+ftype, ftype);
				this.setAttribute('src', sTempSrc);
			}
		}
	}
}


window.onload = initRollovers;

//---------- フォーム選択時色変更(小)
function toggleColor(objElement)
{
	if (objElement.className=='txtbox')
		objElement.className='focus';
	else
		objElement.className='txtbox';
}

//---------- フォーム選択時色変更(中)
function toggleColorMid(objElement)
{
	if (objElement.className=='txt_midbox')
		objElement.className='focus_midbox';
	else
		objElement.className='txt_midbox';
}

//---------- フォーム選択時色変更(大)
function toggleColorBig(objElement)
{
	if (objElement.className=='txt_bigbox')
		objElement.className='focus_bigbox';
	else
		objElement.className='txt_bigbox';
}

// --------- フォーム選択時ESCキー無効化
function ignoreESC(objElement,code) {
	try{
		var code=code||getEvent().keyCode;
//		var code=e.keyCode;
//		alert("code = "+code);
		if( code==27 ) {
			objElement.blur();
			objElement.focus();
		}
	} catch( e ) {
		alert(e);
	}
}
function getEvent() {
	return window.Event || window.event;
}