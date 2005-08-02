/**********************************************************
Below code derived from Sleight (c) 2001 Aaron Boodman
http://www.youngpup.net
**********************************************************/
window.onload = function() { loadPNGs(); }

function loadPNGs() {
    if (navigator.platform != "Win32"
	|| navigator.appName != "Microsoft Internet Explorer") {
	return;
    }

    for (var i = document.images.length - 1, img = null;
	 (img = document.images[i]); i--) {
	loadPNG(img);
    }
}

function loadPNG(img) {
    if (navigator.platform != "Win32"
	|| navigator.appName != "Microsoft Internet Explorer") {
	return;
    }

    var rslt = navigator.appVersion.match(/MSIE (\d+\.\d+)/, '');
    var itsAllGood = (rslt != null && Number(rslt[1]) >= 5.5);

    if (itsAllGood && img.src.match(/\.png$/i) != null) {
	var src = img.src;
	img.style.width = img.width + "px";
	img.style.height = img.height + "px";
	img.src = "/images/spacer.png";
	img.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + src + "', sizing='scale')";
    }
}
