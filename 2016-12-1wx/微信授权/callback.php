<?php
header("Content-type:text/html;charset=utf8");
$appid = "wx27e2a6b55a85bed6";
$sceret = "dddfa4842b2690e8cec7c5559d3d56ab";
$code = $_GET['code'];
//接收微信回掉的数据的
$url = " https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$sceret&code=$code&grant_type=authorization_code";

$res = httpGet($url);
$res = json_decode($res,true);
$access_token = $res['access_token'];
$openid = $res['openid'];

$url2 = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";

$userinfo = httpGet($url2);
$userinfo = json_decode($userinfo,true);
$openid = $userinfo['openid'];
$nickname = $userinfo['nickname'];
$headimgurl = $userinfo['headimgurl'];
$score = mt_rand(1, 100);

//不同用户的信息存进数据库，相同的用户的话就更新分数
$sql1 = "SELECT * FROM users WHERE openid='$openid'";
$res = getlist($sql1);
 if($res){
 	//已经授权过的用户：更新分数
	$sql2 = "UPDATE users SET score=$score WHERE openid='$openid'";
	$res = update($sql2);
//	if($res){
//		echo "更新成功";
//	}else{
//		echo "更新失败";
//	}
 }else{
 	//没有授权过的用户：添加用户信息及分数
	$sql = "INSERT INTO users (openid,nickname,headimgurl,score) VALUES ('$openid','$nickname','$headimgurl',$score)";
	$reslut = add($sql);
//	if($reslut){
//		echo "添加成功";
//	}else{
//		echo "添加失败";
//	}
 }
 
 //获取到存入数据库的数据并且按照分数排名
 $sql3 = "SELECT * FROM users ORDER BY score DESC";
 $list = getlist($sql3); 
// print_r($list);
 
//echo $userinfo;
//更新数据库
function update($sql){
	//连接数据库
	$db = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

// 连从库
// $db = mysql_connect(SAE_MYSQL_HOST_S.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

if ($db) {
    mysql_select_db(SAE_MYSQL_DB, $db);
    // ...
}
	mysql_query("set names utf8");
	$res = mysql_query($sql);
	if($res){
		return true;	
	}else{
		return false;
	}
}


/**
 * 数据库添加函数
 * $sql  string ：sql语句；
 * @return bool ;
 */
function add($sql){
		$db = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

// 连从库
// $db = mysql_connect(SAE_MYSQL_HOST_S.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

if ($db) {
    mysql_select_db(SAE_MYSQL_DB, $db);

    // ...
}
	mysql_query( "set names utf8");
	$res = mysql_query( $sql);
	
//	$reslut = mysqli_insert_id($link);
//	echo $reslut;die;
	if($res){
		return true;	
	}else{
		return false;
	}
}


function getlist($sql){
		$db = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

// 连从库
// $db = mysql_connect(SAE_MYSQL_HOST_S.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);

if ($db) {
    mysql_select_db(SAE_MYSQL_DB, $db);
    // ...
}
	mysql_query("set names utf8");
	//执行sql
	$res = mysql_query($sql);
	while($list = mysql_fetch_assoc($res)){
		$arr[] = $list;
	}
	if(!empty($arr)){
		return $arr;
	}else{
		return false;
	}
}

//httpPost  给微信服务器传递参数  返还相应的数据。
//1.初始化curl
//2.配置curl
//3.执行curl
//4.关闭curl
function httpPost($data,$url){
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
         curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
         curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         $tmpInfo = curl_exec($ch);
         if (curl_errno($ch)) {
          return curl_error($ch);
       	}
       	curl_close($ch);
        return $tmpInfo;
    }


//通过php来请求微信接口api
function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//  curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
 }

?>



<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>用户列表</title>
	</head>
	<body>
		<table border="">
			<tr><th>昵称</th><th>图像</th><th>分数</th></tr>
			
			
			<?php foreach($list as $k=>$v){ ?>
				<tr>
					<td><?php echo $v['nickname'] ?></td>
					<td>
						<img width="50px" height="50px" src="<?php echo $v['headimgurl'] ?>" />
						</td>
						<td><?php echo $v['score'] ?></td></tr>
			<?php } ?>
	
			
			
			
		</table>
	</body>
	
</html>

