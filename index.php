<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="description" content="twitter trends for last 24 hours.">
<meta name="keywords" content="twitter,trend">
<title>twitter trends for last 24 hours</title>
<style>
body{font-size:12px;font-family:sans-serif;background-color:#222;color:#fefefe;margin:0px;padding:0px;}
.trend-box{width:200px;float:left;margin:0 10px;background-color:#333;}
.trend-box ul{padding:0px;margin:0px;}
.trend-box li{padding:3px;background-color:#e0e0e0;border-bottom:1px solid #555;list-style-type:none;}
.trend-box li:hover{opacity:0.95}
.trend-box li a{color:#222;text-decoration:none;}
.trend-box .time{text-align:center; font-weight:bold;}
h1{text-align:left;font-size:1.1em;width:450px;float:left;margin:0px;padding:0px;line-height:1.5em;}
h1 a{color:#1FC4FF;}
a{text-decoration:none;}
header{display:block;margin:0px;padding:10px;overflow:hidden;background:#111;}
header .advt{width:546px;float:left;padding:3px;}
#container{width:100%;overflow:hidden;margin:0 auto 20px;}
#content{width:5300px;}
#scroller{width:5300px;height:30px;cursor:ew-resize;}
#forkoff{color:#eee;padding:0.5em;position:absolute;right:0;top:0;font-size:0.8em;}
</style>
<!--[if IE]><script>var e="abbr,article,aside,audio,canvas,datalist,details,figure,footer,header,hgroup,mark,menu,meter,nav,output,progress,section,time,video".split(',');var i=e.length;while(i--){document.createElement(e[i]);}</script><![endif]-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
<script src="jquery.prettydate.js" type="text/javascript"></script>
<script>
$(function(){
    /* add pretty date */
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
    /* scrolling on mousemove and right,left key */
    var container = $('#container'),
        content = $('#content'),
        box_padding = 10,
        container_width = container.width(),
        last_box = content.find('.trend-box:last-child');

    container.css({overflow: 'hidden'});

    $("#scroller").mousemove(function(e){
        var content_width = last_box[0].offsetLeft + last_box.outerWidth() + box_padding;
        var left = (e.pageX - container.offset().left) * (content_width-container_width) / container_width;
        container.scrollLeft(left);
    });

    $("*").keypress(function (e) {
        if(e.keyCode == 39 ){
            var pos = -$("#content").offset().left;
            var left = pos + 50;
        }
        else if(e.keyCode == 37 ){
            var pos = -$("#content").offset().left;
            var left = pos - 50;
        }

        if(left>content.width()){
            left = content.width();
        }
        else if(left<0){
            left = 0;
        }
        container.scrollLeft(left);
    });
	
});
</script>
</head>
<body>
<header>
	<h1><a href="">twitter trends for last 24 hours</a></h1>
        <div class="advt">
        </div>
    <a id="forkoff" href="http://github.com/vsr/trends">Fork off!</a>
</header>
<div id="container">
<div id="scroller"></div>
<div id="content">

<?php

function getfilecontents($url){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_USERAGENT, "trends- USER-AGENT ");
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

$color_array = array(
					'#F0F8FF','#EFDECD','#CD9575','#8DB600','#FAE7B5','#F5F5DC','#FFC1CC',
					'#F0DC82','#DEB887','#99BADD','#FBEC5D','#FFFF31','#BDB76B','#EEDC82',
					'#FCF75E','#BDDA57','#C3B091','#CCCCFF','#BFFF00','#F3E5AB','#FFCC99',
					'#B57DDF','#FFBCAF','#8FFFC8','#94FF5F','#B7AFFF','#5FFFA2','#E0FF5F',

			);


$url = "http://api.twitter.com/1/trends/daily.json";
$file = "trends.json";
$processed_file = "trends-processed.json";
$trend_array = array();
$term_color_array = array();
$cache_period = 60*30; /* time after which cache is to be flushed: in seconds */

if( !editedRecently( $cache_period, $file) ){
	$response = getfilecontents( $url );
	if( $response[0]==true){
		file_put_contents( $file, $response[2]);
	}
}

if( !editedRecently( $cache_period, $processed_file) ){

    $data = json_decode( file_get_contents( $file ), true);
    foreach($data['trends'] as $time=>$trends){
        $strtime =  strtotime($time);
        $trend_a = array();
        foreach($trends as $trend){
            $trend_a[] = array( 'query' => $trend['query'],  'name' => $trend['name'] );
        }
        $trend_array[$strtime] = $trend_a;
    }
    krsort($trend_array);
    file_put_contents( $processed_file, json_encode($trend_array) );
}

$trend_array = json_decode( file_get_contents( $processed_file ), true);

foreach($trend_array as $datetime=>$trends){
	$time = date( "d-M-Y H:i:s", $datetime );
	$iso_date = date("Y/m/d H:i:s", $datetime);
	echo "<div class='trend-box'><p class='time' data-timestamp='{$iso_date}'>{$time}</p><ul>";
	foreach($trends as $trend){
        if( !isset($term_color_array[$trend['name']]) ){
            $term_color_array[$trend['name']] = $color_array[ rand(0, count($color_array)) ];
        }
		echo "<li style='background-color:".$term_color_array[$trend['name']]."' > <a target='twittersearch' href='http://search.twitter.com/search?q=".urlencode($trend['query'])."'>{$trend['name']}</a> </li>";
	}
	echo "</ul></div>";
}
?>
</div>
</div>
</body>
</html>
