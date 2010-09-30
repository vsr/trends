<?php
error_reporting( E_ALL);
function getfilecontents($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_USERAGENT, "@vsr - rockraikar@gmail.com");
	$res=curl_exec($ch);
	$info=curl_getinfo($ch);
	curl_close($ch);
	if($res === false || $info['http_code'] !=200 ) { $ret=array(false,$info['http_code'],$res);}
	else{$ret=array(true,$info['http_code'],$res);}
	return $ret;
}


function editedRecently($secs,$filename){
	//if we have it in cache
	if (file_exists($filename)) {
		$edittime= filemtime($filename);
		$time=time();
		if($time>=($edittime+$secs)){ return false;}
		else {return true;}
	}
	else {return false;}
}



$url = "http://api.twitter.com/1/trends/daily.json";
$file = "trends.json";

if( !editedRecently( 60*30, $file) ){
	$response = getfilecontents( $url );
	if( $response[0]==true){
		file_put_contents( $file, $response[2]);
	}
}

$data = json_decode( file_get_contents( $file ), true);
$trend_array = array();

$term_array = array();

$color_array = array(
					'#F0F8FF','#EFDECD','#CD9575','#8DB600','#FAE7B5','#F5F5DC','#FFC1CC',
					'#F0DC82','#DEB887','#99BADD','#FBEC5D','#FFFF31','#BDB76B','#EEDC82',
					'#FCF75E','#BDDA57','#C3B091','#CCCCFF','#BFFF00','#F3E5AB','#FFCC99',
					'#B57DDF','#FFBCAF','#8FFFC8','#94FF5F','#B7AFFF','#5FFFA2','#E0FF5F',

			);

foreach($data['trends'] as $time=>$trends){
//	echo "<p>$time</p><p>";
	$strtime =  strtotime($time);
//	echo date( "H:i:s", $strtime );
	$trend_a = array();
	foreach($trends as $trend){
//		echo " {$trend['query']}, ";
		$trend_a[] = $trend;
		$term_array[$trend['name']] = $color_array[ rand(0, count($color_array)) ];
	}
	$trend_array[$strtime] = $trend_a;
//	echo "</p>";
}

krsort($trend_array);

?>
<!doctype html>
<html lang="en">
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js" type="text/javascript"></script>
<script>
/*
 * JavaScript Pretty Date
 * Copyright (c) 2008 John Resig (jquery.com)
 * Licensed under the MIT license.
 */

// Takes an ISO time and returns a string representing how
// long ago the date represents.
function prettyDate(time){
	var date = new Date((time || "")),
		diff = (((new Date()).getTime() - date.getTime()) / 1000),
		day_diff = Math.floor(diff / 86400);
			
	if ( isNaN(day_diff) || day_diff < 0 || day_diff >= 31 )
		return;
			
	return day_diff == 0 && (
			diff < 60 && "just now" ||
			diff < 120 && "1 minute ago" ||
			diff < 3600 && Math.floor( diff / 60 ) + " minutes ago" ||
			diff < 7200 && "1 hour ago" ||
			diff < 86400 && Math.floor( diff / 3600 ) + " hours ago") ||
		day_diff == 1 && "Yesterday" ||
		day_diff < 7 && day_diff + " days ago" ||
		day_diff < 31 && Math.ceil( day_diff / 7 ) + " weeks ago";
}

// If jQuery is included in the page, adds a jQuery plugin to handle it as well
if ( typeof jQuery != "undefined" )
	jQuery.fn.prettyDate = function(){
		return this.each(function(){
			var date = prettyDate(this.title);
			if ( date )
				jQuery(this).text( date );
		});
	};

</script>
<script>
$(function(){
	
	$(".trend-box .time").each(function(){
		var t = Date.parse( $(this).attr("data-timestamp") );
		var datetime = new Date(t);
		var d = new Date();
		d.setUTCFullYear(datetime.getFullYear());
		d.setUTCMonth(datetime.getMonth());
		d.setUTCDate(datetime.getDate());
		d.setUTCHours(datetime.getHours());
		d.setUTCMinutes(datetime.getMinutes());
		d.setUTCSeconds(datetime.getSeconds());

		$(this).html( prettyDate( d ) );
	});
	
var div = $('#container'),
                 ul = $('#content'),
                 // unordered list's left margin
                 ulPadding = 10;

    //Get menu width
    var divWidth = div.width();

    //Remove scrollbars
    div.css({overflow: 'hidden'});

    //Find last image container
    var lastLi = ul.find('.trend-box:last-child');
	var mouse_pos = 0;
    $("#scroller").mousemove(function(e){
		mouse_pos = e.pageX;
      var ulWidth = lastLi[0].offsetLeft + lastLi.outerWidth() + ulPadding;
      var left = (e.pageX - div.offset().left) * (ulWidth-divWidth) / divWidth;
      div.scrollLeft(left);

    });

	$("*").keypress(function (e) {

			var ulWidth = lastLi[0].offsetLeft + lastLi.outerWidth() + ulPadding;
			if(e.keyCode == 39 ){
				var pos = -$("#content").offset().left;
				var left = pos + 50;

			}
			else if(e.keyCode == 37 ){
				var pos = -$("#content").offset().left;
				var left = pos - 50;

			}

			if(left>ul.width()){ left = ul.width();}
			else if(left<0){ left = 0;}
			if(mouse_pos>ul.width()){ mouse_pos = ul.width();}
			else if(mouse_pos<0){ mouse_pos = 0;}
			div.scrollLeft(left);

	});

	
	
	
});
</script>
<style>
body{font-size:12px;font-family:sans-serif;background-color:#222;color:#fefefe;margin:0px;padding:0px;}
.trend-box{width:200px;float:left;margin:0 10px;background-color:#333;}
.trend-box ul{padding:0px;margin:0px;}
.trend-box li{padding:3px;background-color:#e0e0e0;border-bottom:1px solid #555;list-style-type:none;}
.trend-box li:hover{opacity:0.95}
.trend-box li a{color:#222;text-decoration:none;}
.trend-box .time{text-align:center; font-weight:bold;}
h1{text-align:left;font-size:1.5em;width:250px;float:left;margin:0px;padding:0px;}
h1 a{color:#1FC4FF;}
a{text-decoration:none;}
header{display:block;margin:0px;padding:10px;background-color:#00171F;overflow:hidden;}
#container{width:100%;overflow:hidden;margin:0 auto 20px;}
#content{width:5300px;}
#scroller{width:5300px;height:30px;cursor:ew-resize;}
</style>
</head>
<body>
<header>
	<h1><a href="">today's twitter trends</a></h1>
</header>
<div id="container">
<div id="scroller"></div>
<div id="content">
<?php

foreach($trend_array as $datetime=>$trends){

	$time = date( "d-M-Y H:i:s", $datetime );
	$iso_date = date("Y/M/d H:i:s", $datetime);
	echo "<div class='trend-box'><p class='time' data-timestamp='{$iso_date}'>{$time}</p><ul>";
	foreach($trends as $trend){
		echo "<li style='background-color:".$term_array[$trend['name']]."' > <a target='twittersearch' href='http://search.twitter.com/search?q=".urlencode($trend['query'])."'>{$trend['name']}</a> </li>";
	}
	echo "</ul></div>";

}
//print_r($trend_array);
?>
</div>
</div>
</body>
</html>
