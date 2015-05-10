// --------- ページ内タブメニュー
function seltab(bpref, hpref, id_max, selected) {
	if (! document.getElementById) return;
	for (var j = 0; j <= id_max; j++) {
		if (! document.getElementById(bpref + j)) continue;
        console.log(j == selected);
		if (j == selected) {
			document.getElementById(bpref + j).style.display = "block";
			document.getElementById(bpref + j).style.position = "relative";
			document.getElementById(hpref + j).className = "open";
            $("[tab=belongTo_"+bpref + j+"]").show();
		} else {
			document.getElementById(bpref + j).style.display = "none";
			document.getElementById(bpref + j).style.position = "relative";
			document.getElementById(hpref + j).className = "close";
            $("[tab=belongTo_"+bpref + j+"]").hide();
		}
	}
}
window.onload = function(){
 seltab('box', 'head', 2, 1);
};