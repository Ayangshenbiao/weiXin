<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>引导页</title>
	</head>
	<body>
		<p>很酷炫的页面</p>
		<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx27e2a6b55a85bed6&redirect_uri=<?php  echo urlencode('http://yuweihai.applinzi.com/0707/callback.php'); ?>&response_type=code&scope=snsapi_userinfo&state=<?php $rand = rand(); echo $rand;  ?>#wechat_redirect">点击我进行授权</a>
	</body>
</html>
