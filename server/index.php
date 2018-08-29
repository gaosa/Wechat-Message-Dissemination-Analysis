<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');
$appId = 'wx0707b11c52253e47';
$appsecret = '3a6f26c507a967212d4b61f2f44fedb9';
$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if (empty($_GET['code'])) {
    header('Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx0707b11c52253e47&redirect_uri='.urlencode($url).'&response_type=code&scope=snsapi_base&state=123#wechat_redirect');
    exit();
}
$timestamp = time();
$jsapi_ticket = make_ticket($appId,$appsecret);
$nonceStr = make_nonceStr();
$signature = make_signature($nonceStr,$timestamp,$jsapi_ticket,$url);
$openid = get_openid($appId,$appsecret);
record_path($openid);

function make_nonceStr()
{
	$codeSet = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	for ($i = 0; $i<16; $i++) {
		$codes[$i] = $codeSet[mt_rand(0, strlen($codeSet)-1)];
	}
	$nonceStr = implode($codes);
	return $nonceStr;
}
function make_signature($nonceStr,$timestamp,$jsapi_ticket,$url)
{
	$tmpArr = array(
	'noncestr' => $nonceStr,
	'timestamp' => $timestamp,
	'jsapi_ticket' => $jsapi_ticket,
	'url' => $url
	);
	ksort($tmpArr, SORT_STRING);
	$string1 = http_build_query( $tmpArr );
	$string1 = urldecode( $string1 );
	$signature = sha1( $string1 );
	return $signature;
}

function make_ticket($appId,$appsecret)
{
	// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
	$data = json_decode(file_get_contents("access_token.json"));
	if ($data->expire_time < time()) {
		$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$appsecret;
		$json = file_get_contents($TOKEN_URL);
		$result = json_decode($json,true);
		$access_token = $result['access_token'];
		if ($access_token) {
			$data->expire_time = time() + 7000;
			$data->access_token = $access_token;
			$fp = fopen("access_token.json", "w");
			fwrite($fp, json_encode($data));
			fclose($fp);
		}
	}else{
		$access_token = $data->access_token;
	}
	// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
	$data = json_decode(file_get_contents("jsapi_ticket.json"));
	if ($data->expire_time < time()) {
		$ticket_URL="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
		$json = file_get_contents($ticket_URL);
		$result = json_decode($json,true);
		$ticket = $result['ticket'];
		if ($ticket) {
			$data->expire_time = time() + 7000;
			$data->jsapi_ticket = $ticket;
			$fp = fopen("jsapi_ticket.json", "w");
			fwrite($fp, json_encode($data));
			fclose($fp);
		}
	}else{
		$ticket = $data->jsapi_ticket;
	}
	return $ticket;
}

function get_openid($appId,$appsecret) {
    $code = $_GET["code"];
    $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='. $appId .'&secret='. $appsecret .'&code='. $code .'&grant_type=authorization_code';
    $json = file_get_contents($url);
    $result = json_decode($json, true);
    $openid = $result['openid'];
    return $openid;
}

function record_path($openid) {

    $db = mysqli_connect("127.0.0.1", "root", "", "db_GSA");
    $query = "INSERT INTO path(From_ID, To_ID, Time) VALUES('".$_GET['old']."','".$openid."',".time().")";
    $result = mysqli_query($db, $query);
    if (!$result) {
	print "Error - the query could not be executed";
	$error = mysqli_error($db);
	print "<p>" . $error . "</p>";
	exit;
    }
}

?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<title>齐泽克笑话选</title>
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0" />
	<link rel="stylesheet" href="http://imgcache.gtimg.cn/mediastyle/mobile/event/20150114_spring_promotion/index.css">
	<script language="JavaScript">
        window.openid = '<?=$openid?>';
    	</script>
</head>
<body class="bodybox">
<script>
    window.debug = true;
</script>
<div class="wrap" id="wrap">
	<div class="item item-1">
        <div class="box">
        	<center>
            <h1>斯拉沃热·齐泽克</h1>
            <br>
            <h3>斯洛文尼亚社会学家、哲学家与文化批判家，</h3>
            <h3>也是心理分析理论家。</h3>
            <br>
            <h3>尤为喜欢、特别擅长用笑话来解释哲学。</h3>
            <br>
	    	<h3>通过笑话，人们将了解到这些情境的可笑。</h3>
	    	<h3>它们不再必然合理，甚至是可以抛诸脑后的东西。</h3>
	    	<br>
	    	<h3>正如维特根斯坦所说：</h3>
	    	<h1>「严肃地道的哲学著作<br>完全可以由笑话写成。」</h1>
	    	<img style="width:100%;" src="http://www.newstatesman.com/sites/default/files/styles/nodeimage/public/blogs_2015/03/2015_11_zizek_books.jpg?itok=-eLj3nSb"/>
	    	</center>
        </div>
    	</div>
    

    <div class="item item-2">
        <div class="box">
        	<center>
           	<h1>一个关于做饭的笑话</h1>
           	<br>
            <h3>任何人<br>都可以在一个小时内做好一锅汤：</h3>
            <h3>备齐所有食材<br>菜切好，烧开水，把食材都放进去<br>小火煨半个小时，偶尔搅拌一下<br>45分钟后，你发现汤没什么味也没法喝<br>倒掉它，打开一罐好的汤罐头<br>放进微波炉，快速热一下<h1>这就是<br>我们每个人都会的做汤方式。</h1>
			<img style="width:100%;" src="https://cdn.meme.am/cache/instances/folder28/500x/67766028/slavoj-zizek-memes-are-ideology.jpg"/>
			</center>
        </div>
    </div>

    <div class="item item-3">
        <div class="box">
        	<center>
            <h3>某人看到朋友<br>竟然在和费德勒打网球</h3>
            <h3>就问：「怎么做到的？」<br>朋友说，他有条金鱼，能满足愿望<br>于是这人就去看了金鱼<br>说：「我想要一柜子钱。」<br>但他打开柜子，发现是一柜子蜂蜜<br>他就抱怨：要的是<font color="black">money</font>，不是<font color="black">honey</font>啊。<br>朋友耸耸肩<br>这金鱼听力是挺差<br>你以为我想变强的是我的</h3>
            <h1><font color="black">TENNIS?</font></h1>
			<img style="width:100%;" src="http://i.imgur.com/EqFpq4E.jpg"/>
			</center>
        </div>
    </div>

    <div class="item item-4">
        <div class="box">
        	<center>
            <h1>蒙古统治下的俄国</h1>
            <br>
            <h3>一农夫和妻子走在尘土小路<br>被蒙古武士喝住，对其妻“就地正法”<br>因为路上太脏<br>武士命一旁的农夫托住他的蛋<br>以免弄脏<br>完事后武士骑马绝尘而去，农夫哈哈大笑<br>妻子怒：「你的老婆刚被玷污，你还笑得出？」<br>农夫说，你不知道，我托他蛋前</h3>
            <h1>先故意蹭了一手泥巴</h1>
            <br>
			<img style="width:80%;" src="https://pics.onsizzle.com/marx-stalin-hegel-engels-trotsky-keynes-memes-doctos-lenin-proletariado-16256933.png"/>
			</center>
        </div>
    </div>

    <div class="item item-5">
        <div class="box">
        	<center>
            <h1>20世纪30年代中期</h1>
            <h3>布尔什维克政治局内吵得不可开交<br>问题是：共产主义是有货币，还是没有货币呢？<br>左翼托洛斯基派称共产主义不用货币<br>货币是私有制社会里才有的<br>右翼布哈拉派称共产主义里肯定有货币<br>由于每一个复杂社会都需要用货币来调节产品交换<br>最后，斯大林打断了两派的争论<br>他同时否定了两派的观点<br>称真实情况是两派意见的高度辩证综合<br>当其他政治局委员问斯大林，这种综合到底是啥样子啊<br>斯大林淡定地回答道</h3>
            <h1>有钱，又没有钱<br>有些人有钱，有些人没钱</h1>
			<img style="width:100%;" src="http://s2.quickmeme.com/img/85/853e971fb9dabe982c32f094ecdcf3d8532dadc3e8639ddff2d907c25aae1dc5.jpg"/>
		</center>
        </div>
    </div>

    <div class="item item-6">
        <div class="box">
        	<center>
            <h1>有一个南斯拉夫警察回到家</h1>
            <br>
            <h3>没想到老婆正光着身子，满脸热潮<br>他心想，屋子里肯定有人<br>他满屋子找奸夫<br>当他把头探到床底下的时候<br>他老婆脸色的立马煞白<br>但是片刻耳语过后<br>这位丈夫心满意足地起身<br>得意洋洋地说道<br>「不好意思啊，亲爱的老婆<br>让你虚惊一场了，床底下没人！」<br>同时，他手里攥着</h3>
            <h1>一摞大面额钞票</h1>
			<img style="width:100%;" src="http://pobierak.jeja.pl/images/2/5/3/147736_chcesz-budowac-komunizm.jpg"/>
		</center>
        </div>
    </div>

	<div class="item item-7">
        <div class="box">
        	<center>
            <h1><font color="black">富人告诉仆人</font></h1>
            <br>
            <h1><font color="black">「把这个穷要饭的赶出去！<br>我太脆弱了<br>看不得别人受苦！」</font></h1>
            <br>
			<img style="width:100%;" src="https://cdn.meme.am/cache/instances/folder320/250x250/63161320/slavoj-zizek-what-we-cannot-speak-about-we-must-pass-over-in-silence-thomas-aquinas.jpg"/>
			</center>
        </div>
    </div>


	<div class="item item-8">
        <div class="box">
        	<center>
            <h1><font color="black">雅鲁泽尔斯基时期的波兰</font></h1>
            <br>
            <h3><font color="black">当时军事政变刚结束<br>那个时期，军队的巡逻兵在宵禁（十点）以后<br>有权不加警告地射击路上的行人<br>两个士兵在巡逻，其中一个看到<br>有人在差十分十点的时候急匆匆的走在路上<br>马上开枪打了他<br>他的同伴问他为什么开枪，毕竟才差十分十点<br>他答道<br>我知道那个家伙——<br>他住得离这儿很远，无论如何十分钟内也到不了家</font></h3>
            <h1><font color="black">所以为了省事儿<br>我现在就把他干掉</font></h1>
            <br>
	<img style="height:260px;" src="http://www.teoriacriativa.com/wp-content/uploads/2012/04/poster-zizek.jpg"/>
			</center>
        </div>
    </div>

</div>

<!-- 加载提示 _S -->
<div class="global">
    <div class="slider"><span class="sprite_global"></span></div>
</div>
<div class="mod_loading" id="loading" style="display:none">
    <div class="content">
        <div class="progress"><span id="progress_bg" style="width:0;"></span></div>
        <p>Loading...</p>
    </div>
</div>
<!-- 加载提示 _E -->
<audio autoplay="" style="height:0;width:0;display:none" ></audio>
<!-- 播放状态 _S -->
<!-- btn_pause为暂停状态 -->
<div class="sprite_global btn_play" id="J_btnMusic" style="display:none"><i class="sprite_global"></i></div>
<!-- 播放状态 _E -->
<!-- 滑块 _E -->
<div class="m_share" style="display:none;" id="shareLayer">
    <!--这里的箭头指向要做判断，不同的平台指向不同-->
    <!--箭头向上-->
    <img id="shareimgup" src="http://imgcache.qq.com/mediastyle/mobile/event/20140318_ceremony_live/img/share_top.png?max_age=2592000" class="top" alt="点击上方分享按钮分享"/>
    <!--箭头向下-->
    <img id="shareimgdown" src="http://imgcache.qq.com/mediastyle/mobile/event/20140318_ceremony_live/img/share_bottom.png?max_age=2592000" class="bottom" alt="点击下方分享按钮分享"  style="display:none;"/>
</div>


<script type="text/javascript" src="iSlider.js"></script> 

<script>

//demo
//用法
var myslider=new iSlider({
    wrap:'#wrap',
    item:'.item',
    onslide:function (index) {
	var data = new FormData();
	data.append("uid", ""+window.openid);
	data.append("index", ""+index);
	data.append("db", "pages");
	var xhr = new XMLHttpRequest();
	xhr.open('post', 'store.php', true);
	xhr.send(data);
    }
});
console.info(myslider);


</script>
</body>
<script src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script>
	wx.config({
		debug: false,
		appId: '<?=$appId?>',
		timestamp: <?=$timestamp?>,
		nonceStr: '<?=$nonceStr?>',
		signature: '<?=$signature?>',
		jsApiList: [
			'onMenuShareTimeline',
			'onMenuShareAppMessage',
			//'getLocation',
			'checkJsApi'
	  ]
	});
</script>
<script>
wx.ready(function () {
	wx.onMenuShareAppMessage({
		title: '齐泽克笑话选',
		desc: '好的笑话本身就是出色的哲学。',
		link: 'http://pkucareer.com/GSA/index.php?old='+window.openid,
		imgUrl: 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1495863316800&di=44eaf4f244c7e07031b35ed63c788c2f&imgtype=jpg&src=http%3A%2F%2Fimg4.imgtn.bdimg.com%2Fit%2Fu%3D1525528989%2C1838838941%26fm%3D214%26gp%3D0.jpg',
		success: function () {
			var data = new FormData();
	        data.append("uid", ""+window.openid);
	        data.append("db", "share");
	        var xhr = new XMLHttpRequest();
	        xhr.open('post', 'store.php', true);
	        xhr.send(data);
		}
    });
    wx.onMenuShareTimeline({
		title: '齐泽克笑话选',
		link: 'http://pkucareer.com/GSA/index.php?old='+window.openid,
		imgUrl: 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1495863316800&di=44eaf4f244c7e07031b35ed63c788c2f&imgtype=jpg&src=http%3A%2F%2Fimg4.imgtn.bdimg.com%2Fit%2Fu%3D1525528989%2C1838838941%26fm%3D214%26gp%3D0.jpg',
		success: function () {
	        var data = new FormData();
	       	data.append("uid", ""+window.openid);
	        data.append("db", "share");
	        var xhr = new XMLHttpRequest();
	        xhr.open('post', 'store.php', true);
	        xhr.send(data);

		}
    });
});
wx.error(function (res) {
 	alert(res.errMsg);
});
</script>
<!--<script src="./demo.js"></script>-->
</html>
