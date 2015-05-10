//---------- ページスクロール
var scrj = 1;
function softScrollBack() {
	if(navigator.appName == "Microsoft Internet Explorer" && document.compatMode == "CSS1Compat") {
		var scdist = document.body.parentNode.scrollTop;
	} else if ((!document.all || window.opera) && document.getElementById) {
		var scdist = document.body.parentNode.scrollTop;
	} else {
		var scdist = document.body.scrollTop;
	}

	if(scrj<50 && scdist) {
		scdist = (scdist>2) ? Math.ceil(scdist*.2) : 1;
		scrj++;
		scrollBy(0,-scdist);
		setTimeout("softScrollBack()",20);
	} else {
			scrollTo(0,0);
			scrj = 1;
	}
}

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