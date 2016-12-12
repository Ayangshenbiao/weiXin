<?php 

	//sae 版本连接数据库
    mysql_connect(SAE_MYSQL_HOST_M.":".SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
	mysql_select_db(SAE_MYSQL_DB);
	
	
/**
 * 构建jssdk 类
 */
class JSSDK{
	//普通连接数据库 链接数据库
	//mysql_connect('localhost','root','');
	//mysql_select_db("425");
	private $appId;
	private $appSecret;
	//接收appid secret
	function __construct($appid,$appSecret) {
		$this->appId = $appid;
		$this->appSecret = $appSecret;
	}
	//请求借口方法
	private function httpGet($url) {
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
	    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
	    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, true);
	    curl_setopt($curl, CURLOPT_URL, $url);
	
	    $res = curl_exec($curl);
	    curl_close($curl);
	
	    return $res;
	}
	//返回微信设置参数
  	public function getSignPackage() {
	    $jsapiTicket = $this->getJsApiTicket();
	
	    // 注意 URL 一定要动态获取，不能 hardcode.
	    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	
	    $timestamp = time();
	    $nonceStr = $this->createNonceStr();
	
	    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
	    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
	
	    $signature = sha1($string);
	
	    $signPackage = array(
	      "appId"     => $this->appId,
	      "nonceStr"  => $nonceStr,
	      "timestamp" => $timestamp,
	      "url"       => $url,
	      "signature" => $signature,
	      "rawString" => $string
	    );
	    return $signPackage; 
  	}
	//随机字符串
	private function createNonceStr($length = 16) {
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    $str = "";
	    for ($i = 0; $i < $length; $i++) {
	      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	    }
	    return $str;
	}
	//获取微信 jsapiticket
	private function getJsApiTicket(){
		
		$query = "SELECT * FROM weixinTicket";
		$result = mysql_query($query);
		if(mysql_num_rows($result)>0){
			//已存在ticket
			$row = mysql_fetch_assoc($result);
			$time = $row['time'];
			if(time()>$time+7000){
				//超时
				$accessToken = $this->getAccessToken();
				$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
				$res=json_decode($this->httpGet($url));
				$ticket = $res->ticket;
				$now = time();
				$query = "UPDATE weixinTicket SET $ticket='{ticket}',time='{$now}' WHERE id={$row['id']}";
				mysql_query($query);
			}else{
				//未超时k
				$ticket=$row['ticket'];
			}
		}else{
			//不存在token
			$accessToken = $this->getAccessToken();
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$res=json_decode($this->httpGet($url));
			$ticket = $res->ticket;
			$time = time();
			$query = "INSERT INTO weixinTicket(id,ticket,time) VALUES(null,'{$ticket}','{$time}')";
			mysql_query($query);
			
		}
		//返回token
		return $ticket;
	}
	//获取token
	private function getAccessToken(){
		
		$query = "SELECT * FROM weixinToken";
		$result = mysql_query($query);
		if(mysql_num_rows($result)>0){
			//已存在token
			$row = mysql_fetch_assoc($result);
			$time = $row['time'];
			if(time()>$time+7000){
				//超时
				$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
				$res=json_decode($this->httpGet($url));
				$access_token = $res->access_token;
				$now = time();
				$query = "UPDATE weixinToken SET token='{$access_token}',time='{$now}' WHERE id={$row['id']}";
				mysql_query($query);
			}else{
				//未超时
				$access_token=$row['token'];
			}
		}else{
			//不存在token
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			$res=json_decode($this->httpGet($url));
			$access_token = $res->access_token;
			$time = time();
			$query = "INSERT INTO weixinToken(id,token,time) VALUES(null,'{$access_token}','{$time}')";
			mysql_query($query);
			
		}
		//返回token
		return $access_token;
	}
	
}





?>