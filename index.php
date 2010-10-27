<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="description" content="twitter trends for last 24 hours.">
<meta name="keywords" content="twitter,trend">
<title>twitter trends for last 24 hours</title>
<style>
*{margin:0;padding:0;border:0;}
body{font-size:12px;font-family:"Droid Serif",sans-serif;background-color:#222;color:#fefefe;margin:0px;padding:0px;}
.trend-box{width:200px;float:left;margin:0 10px;}
.trend-box ul{padding:0px;margin:0px;}
.trend-box li{padding:3px;background-color:#e0e0e0;border-bottom:1px solid #555;list-style-type:none;cursor:pointer;text-indent:0.5em;}
.trend-box li:hover{opacity:0.95}
.trend-box li a{color:#222;text-decoration:none;}
.trend-box .time{text-align:center; font-weight:bold;padding:5px;font-size:0.9em;margin:0 -1px;cursor:pointer;}
h1{text-align:left;font-size:1.5em;width:450px;float:left;margin:0px;padding:0px;line-height:1.5em;}
h1 a{color:#1FC4FF;}
a{text-decoration:none;}
header{display:block;margin:0px;padding:10px;overflow:hidden;background:#111;}
header .advt{width:546px;float:left;padding:3px;}
#container{width:100%;overflow:hidden;margin:0 auto 20px;}
#content{width:5300px;}
#scroller{width:5300px;height:30px;cursor:ew-resize;}
.search-box{background-color:#FAFAFA;color:#222222;max-height:455px;min-height:100px;overflow:scroll;padding:5px;width:400px;}
.tweetlist{padding:5px 0;overflow:hidden;}
.tweetlist li{display:block;float:left;padding:2px 5px;width:100%;
	border-bottom:1px dotted #666;font-size:0.8em;overflow:hidden;}
.more{cursor:pointer;display:block;text-align:center;width:6em;margin:0 auto;font-size:0.8em;}
.ui-dialog-titlebar{font-size:0.7em;font-weight:bold;}
.tweetlist a.user{color:#1FC4FF;}
.tweetlist .tweet{padding-left:0.5em;}
#error-notification{display:none;position:fixed;width:100%;text-align:center;line-height:1.3em;color:#6F0E07;background-color:#F3FF8F;}
.tweetlist li.loading{text-align:center;border:0;}
.trend-info{background-color:#222222;color:#EEEEEE;font-size:0.8em;padding:2px 13px;}
#attribution{float:left;font-size:0.6em;color:#1FC4FF;}
#attribution a{color:#FAFAFA;}
#help{cursor:help;display:block;margin:10px;position:absolute;right:0;text-indent:999999px;top:0;}
#help-dialog{padding:5px;display:none}
#help-dialog li{list-style-type:disc;list-style:inside;padding:5px 0;}
</style>
<!--[if IE]><script>var e="abbr,article,aside,audio,canvas,datalist,details,figure,footer,header,hgroup,mark,menu,meter,nav,output,progress,section,time,video".split(',');var i=e.length;while(i--){document.createElement(e[i]);}</script><![endif]-->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js" type="text/javascript"></script>
<script src="md5.js" type="text/javascript"></script>
<link media="all" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.3/themes/dark-hive/jquery-ui.css" rel="stylesheet">
<link href='http://fonts.googleapis.com/css?family=Droid+Serif&subset=latin' rel='stylesheet' type='text/css'>
<script src="jquery.prettydate.js" type="text/javascript"></script>
<script>
function notify_error(XMLHttpRequest, textStatus, errorThrown){
    $("#error-notification").text("Something went wrong while contacting twitter."+ textStatus).show();
}

var whatthetrend_url = 'http://api.whatthetrend.com/api/trend/getByName/$trend$/jsonp?api_key=API_KEY&callback=?';
function load_tweets(query, ele, term){

	function render_tweets(data){
        ele.data('query', data.next_page);
        $.each(data.results, function(){
            var user = $("<a></a>").text('@'+this.from_user).attr({'target':'twitteruser', 'href': 'http://twitter.com/'+this.from_user}).addClass('user');
            var tweet = $("<span></span>").html(this.text).addClass('tweet');
            var li = $("<li></li>").append(user).append(tweet);
            $("ul",ele).append(li);
        });
        $("#error-notification").hide();
        $(".loading",ele).remove();
    }

	function render_trend_info(data){
        if( data && data.api && data.api.trend && data.api.trend.blurb && data.api.trend.blurb.text ){
            var p = $('<p></p>').text(data.api.trend.blurb.text).addClass('trend-info ui-corner-all');
            $(ele).prepend(p);
        }
    }

    var li = $("<li></li>").html("<img alt='loading' src='ajax-loader.gif' />").addClass('loading');
    $("ul",ele).append(li);
	$.ajax({
        url: 'http://search.twitter.com/search.json'+query+'&show_user=true&result_type=recent&callback=?',
        dataType: 'jsonp',
        success: render_tweets,
        type: "GET",
        error: notify_error,
        timeout: 1000*20
        });

	$.ajax({
        url: 'http://api.whatthetrend.com/api/trend/getByName/' + term + '/jsonp?api_key=API_KEY&callback=?',
        dataType: 'jsonp',
        success: render_trend_info,
        type: "GET",
        error: notify_error,
        timeout: 1000*20
        });



}

function load_search_dialog(element, event){
	var id_str = MD5.hex_md5(element.text());
	if( $("#"+id_str).length > 0){
		$("#"+id_str).dialog({'position': [event.clientX, event.clientY], "width": 400});
		return;
	}
	else{
		
		var d = $("<div class='search-box'></div>").attr({ "id": id_str, "title": element.text()}).html("<ul class='tweetlist'></ul><a class='more ui-widget-header ui-corner-all'>more</a>");
		$('body').append(d);
		d.dialog({'position': [event.clientX, event.clientY], "width": 400});
		var query = '?page=1&rpp=10&q='+element.attr('data-url');
		d.data('query',query);
		load_tweets(query, $("#"+id_str), element.attr('data-url') );
		$(".more",d).click(function(){ var query = d.data('query'); load_tweets(query , $("#"+id_str), element.attr('data-url') ); });
	}
}

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
        if(e.keyCode == 39 || e.charCode == 46  ){
            var pos = -$("#content").offset().left;
            var left = pos + 50;
        }
        else if(e.keyCode == 37 || e.charCode == 44 ){
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
	
	$(".trend-box li").click(function(ev){ load_search_dialog($("a", this), ev)});
    $("#help").click(function(){  $("#help-dialog").dialog({'width': 500}); });
});
</script>
</head>
<body>
<p id="error-notification"></p>
<header>
	<h1><a href="">twitter trends for last 24 hours</a></h1>
        <div class="advt">
            <script type="text/javascript"><!--
            google_ad_client = "pub-7576293061984551";
            /* trends 468x15, created 9/30/10 */
            google_ad_slot = "0924371936";
            google_ad_width = 468;
            google_ad_height = 15;
            //-->
            </script>
            <script type="text/javascript"
            src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
            </script>
        </div>
        <p id="attribution">data from: <a href="http://dev.twitter.com/doc/get/trends/daily">twitterapi</a>, <a href="http://api.whatthetrend.com/">whatthetrend.com</a>, <a href="http://www.colourlovers.com/api">COLOURlovers.com</a>.</p>
    <a id="help" href="#" class="ui-icon-help ui-icon">Help</a>
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
					/*'#F0DC82','#DEB887','#99BADD','#FBEC5D','#FFFF31','#BDB76B','#EEDC82',
					'#FCF75E','#BDDA57','#C3B091','#CCCCFF','#BFFF00','#F3E5AB','#FFCC99',
					'#B57DDF','#FFBCAF','#8FFFC8','#94FF5F','#B7AFFF','#5FFFA2','#E0FF5F',*/
			);


$url = "http://api.twitter.com/1/trends/daily.json";
$colorlovers_top_url = "http://www.colourlovers.com/api/colors/top?numResults=50&briRange=90,99&hueRange=10,300&format=json";
$colors_file = 'colors.json';
$processed_color_file = 'colors-processed.json';
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

if( !editedRecently( $cache_period*12, $colors_file) ){
	$response = getfilecontents( $colorlovers_top_url );
	if( $response[0]==true){
		file_put_contents( $colors_file, $response[2]);
	}
}

if( !editedRecently( $cache_period*12, $processed_color_file) ){
    $data = json_decode( file_get_contents( $colors_file ), true);
    foreach($data as $color){
        $color_array[] = '#'.$color['hex'];
    }
    file_put_contents( $processed_color_file, json_encode($color_array) );
}

$color_array = json_decode( file_get_contents( $processed_color_file ), true) ;
$trend_array = json_decode( file_get_contents( $processed_file ), true);

shuffle( $color_array );

foreach($trend_array as $datetime=>$trends){
	$time = date( "d-M-Y H:i:s", $datetime );
	$iso_date = date("Y/m/d H:i:s", $datetime);
	echo "<div class='trend-box'><p class='time ui-dialog-titlebar ui-widget-header ui-corner-top ui-helper-clearfix' data-timestamp='{$iso_date}'>{$time}</p><ul>";
	foreach($trends as $trend){
        if( !isset($term_color_array[$trend['name']]) ){
            $term_color_array[$trend['name']] = $color_array[ rand(0, count($color_array)) ];
        }
		echo "<li style='background-color:".$term_color_array[$trend['name']]."' > <a data-url='".urlencode($trend['query'])."'>{$trend['name']}</a> </li>";
	}
	echo "</ul></div>";
}
?>
</div>
</div>
<div id="help-dialog" title="help">
<ul>
    <li>This is a simple app to view the trends on twitter for last 24 hours.</li>
    <li>Use the right, left (or &gt; and &lt;) keyboard keys to scroll right or left.</li>
    <li>Click on any trend item to view the latest search results.</li>
</ul>
</div>
</body>
</html>
