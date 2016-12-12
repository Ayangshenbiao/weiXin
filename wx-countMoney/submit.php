<?php 
	//information
	$username = $_GET["username"];
	$tel = $_GET["tel"];
	$openid = $_GET["openid"];
	$nickname = $_GET["nickname"];
	$headimgurl = $_GET["headimgurl"];
	$score = $_GET["score"];
	
    mysql_connect(SAE_MYSQL_HOST_M.":".SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS);
	mysql_select_db(SAE_MYSQL_DB);
	
	mysql_query("set names utf8");
	
	$query = "SELECT * FROM countMoney WHERE openid='{$openid}'";
	
	$result = mysql_query($query);
	if(mysql_num_rows($result)>0){
		//用户存在
		$row = mysql_fetch_assoc($result);
		if($score>$row['score']){
			//更新数据
			$query = "UPDATE countMoney SET score={$score} WHERE openid='{$openid}'";
			mysql_query($query);
			if(mysql_affected_rows()>0){
				$query = "SELECT * FROM countMoney WHERE score>{$score} ORDER BY score desc";
				$result=mysql_query($query);
				$pw=1;
				while($row = mysql_fetch_row($result)){
					$pw++;
				}
				echo '{"code":2,"msg":"突破成功","pw":'.$pw.'}';
			}
		}else{
			$score = $row['score'];
			$query = "SELECT * FROM countMoney WHERE score>{$score} ORDER BY score desc";
			$result=mysql_query($query);
			$pw=1;
			while($row = mysql_fetch_row($result)){
				$pw++;
			}
			echo '{"code":3,"msg":"突破失败","pw":'.$pw.'}';
		}
		
	}else{
		//用户不存在
		$query = "INSERT INTO countMoney(id,openid,nickname,name,phone,headimgurl,score) VALUES(null,'{$openid}','{$nickname}','{$username}',{$tel},'{$headimgurl}',{$score})";
		mysql_query($query);
		if(mysql_insert_id()>0){
			//插入成功
			$query = "SELECT * FROM countMoney WHERE score>{$score} ORDER BY score desc";
			$result=mysql_query($query);
			$pw=1;
			while($row = mysql_fetch_row($result)){
				$pw++;
			}
			echo '{"code":1,"msg":"插入成功","pw":'.$pw.'}';
		}else{
			echo '{"code":10,"msg":"插入失败"}';
		}
	}
	
	
	function getPW(){
		$query = "SELECT * FROM countMoney WHERE score>{$score} ORDER BY score desc";
		$result=mysql_query($query);
		$pw=1;
		while($row = mysql_fetch_row($result)){
			$pw++;
		}
	}
/*
 * 
 * id
 * headimgurl
 * name
 * phone
 * score
 * nickname
 * openid
 * 
 * 
 * */
 ?>