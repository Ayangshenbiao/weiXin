<?php
////获取token并做缓存；
//$appid = "wxf3cc44cf066c6dd8";
//$secret= "23f0b874e6cd776e42dcd1f497336e43";
//
//$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
//
////$res = httpGet($url);
////print_r(json_decode($res,true));
//
//echo getAccessToken($url);
//
////请求ip的接口
//$url2 = "https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=ht956xQzKPGj3eFxC7PcfElq_06LB-R68I0NBVo-0K_UotvOZrVPbh29Q47HhJWIDPq9148brrWaeNxSvbPJ7LgO5iae6RmF6YkdTB4KHhFkmi_4IW6VZm8F951afZ2QSOUfABACYE";
////$ip = httpGet($url2);
////print_r(json_decode($ip,true));
//
//
//$url3 = "https://api.weixin.qq.com/cgi-bin/shorturl?access_token=ht956xQzKPGj3eFxC7PcfElq_06LB-R68I0NBVo-0K_UotvOZrVPbh29Q47HhJWIDPq9148brrWaeNxSvbPJ7LgO5iae6RmF6YkdTB4KHhFkmi_4IW6VZm8F951afZ2QSOUfABACYE";
//$data['action'] = 'long2short';
//$data['long_url'] = 'http://wap.koudaitong.com/v2/showcase/goods?alias=128wi9shh&spm=h56083&redirect_count=1';
//$data = json_encode($data);
//$shorturl = httpPost($data,$url3);
//echo $shorturl;

//测试号/////////////////////////////////////////////
$testappid = "wxd8e24e163bce894f";
$testsecret = "d790efd8be6d299625f88513316a8d3b";
//请求二维码接口
//获取ticket
$token1 = "Jv3kwE5oxbl2tXVtgGAlet1OHoMXNbjspuWM_fYnKtIi7l5_hcQ35bUoEhoop_ckjECWqVoU2AnMnoPuaGDymQp4jeJi-rSq5KGXfwGePYIMPMbAFAPSP";
$url4 = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$token1}";
$data['expire_seconds'] = 604800;
$data['action_name'] = "QR_SCENE";
$data['action_info']['scene']['scene_id'] = 123;
$data = json_encode($data);
$ticket = httpPost($data,$url4);
$ticket = urlencode(json_decode($ticket,true)['ticket']);
header("Location:https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$ticket}");



////////////////////////////////////////////////////
function getAccessToken($url){
//读取本地的token：如果有并没有过期就直接用，如果有且过期需要更新token文件；如果没有就重新获取token并且创建；
    if(file_exists("token.txt")){
        //有本地储存token的文件
        $res = file_get_contents("token.txt");
        //获取过期时间
        $expires_in = json_decode($res,true)['expires_in'];
        //判断是否过期
        if(time()<$expires_in){
            //没有过期
            $token = json_decode($res,true)['token'];

        }else{
            //过期
            $res = httpGet($url);
            $token = json_decode($res,true)['access_token'];
            $data['token'] = json_decode($res,true)['access_token'];
            $data['expires_in'] = time()+7100;
            $sureData = json_encode($data);
            file_put_contents("token.txt",$sureData);

        }

    }else{
        //没有本地储存的token文件
        $res = httpGet($url);
        $token = json_decode($res,true)['access_token'];
        $data['token'] = json_decode($res,true)['access_token'];
        $data['expires_in'] = time()+7100;
        $sureData = json_encode($data);
        file_put_contents("token.txt",$sureData);

    }

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



















?>