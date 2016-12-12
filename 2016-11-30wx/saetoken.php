<?php
$appid = "wxf3cc44cf066c6dd8";
$secret= "23f0b874e6cd776e42dcd1f497336e43";

$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";

function getAccessToken(){
    //判断是否有token
    $sql = "SELECT * FROM token WHERE id=1";
    $res = getlist($sql);
    if($res){
        //已经储存了;
        if(time()<$res[0]['lastTime']){
            $sureToken = $res[0][''];
        }



    }else{
        //没有储存；
        $data = json_decode(httpGet($url),true);
        $suerData['token'] = $data['access_token'];
        $suerTime = time()+$data['expires_in'];
        $sql = "INSERT INTO token (id,token,lastTime) VALUES (1,$suerTime)";



    }




}



// 更新数据库函数
function update($sql){
	$link = mysqli_connect("localhost","root","","yang");
	mysqli_query($link,"set names utf8");
	$res = mysqli_query($link,$sql);
	if($res){
		return true;
	}else{
		return false;
	}
}

?>