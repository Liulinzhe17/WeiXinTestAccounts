<?php


function getMusicInfo($table,$num)
{
   
    $mysql_host =     "w.rdc.sae.sina.com.cn";//SAE_MYSQL_HOST_M;
    $mysql_user =       "w2w044wxx4"; //SAE_MYSQL_USER;
    $mysql_password =   "w5z14my332mklz33zjiki4yj34kx0mx5l20i11k4";    //SAE_MYSQL_PASS;
    $mysql_database =    "app_yujiage2";     // SAE_MYSQL_DB;
    
	
	$mysql_table = $table;
	$id = rand(1, 190);
   
	$mysqli=new mysqli($mysql_host,$mysql_user,$mysql_password,$mysql_database);
	if($mysqli->connect_error){
	    echo "连接数据库失败：".$mysqli->connect_error;
	    $mysqli=null;
	    exit;
	}
	echo "连接数据库成功！<br/>";

	$musicArray="";
	for ($i=0; $i<$num; $i++) { 
		$ids=$id+$i;
		$sql = "SELECT * FROM `".$mysql_table."` WHERE `id` = '".$ids."'";
		$result=$mysqli->query($sql);

		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
		        // echo "name:".$row["music_name"]."<br>";
				$musicArray=$musicArray.
				$row["music_name"]."\n".
				"排名：".$row["music_rank"].",".
				$row["music_url"].",";
		    }
		}
	}

	$mysqli->close();
	return $musicArray;
  
}

function getComment($table)
{
   
    $mysql_host =     "w.rdc.sae.sina.com.cn";//SAE_MYSQL_HOST_M;
    $mysql_user =       "w2w044wxx4"; //SAE_MYSQL_USER;
    $mysql_password =   "w5z14my332mklz33zjiki4yj34kx0mx5l20i11k4";    //SAE_MYSQL_PASS;
    $mysql_database =    "app_yujiage2";     // SAE_MYSQL_DB;
    
	
	$mysql_table = $table;
	$id = rand(118, 299);
   
	$mysqli=new mysqli($mysql_host,$mysql_user,$mysql_password,$mysql_database);
	if($mysqli->connect_error){
	    echo "连接数据库失败：".$mysqli->connect_error;
	    $mysqli=null;
	    exit;
	}
	echo "连接数据库成功！<br/>";

	$musicArray="";
	$sql = "SELECT * FROM `".$mysql_table."` WHERE `id` = '".$id."'";
	$result=$mysqli->query($sql);

	if ($result->num_rows > 0) {
	    while($row = $result->fetch_assoc()) {
			$musicArray=$musicArray.
			"✉️：".$row["content"]."\n"."\n".
            "🎵：《".$row["song_name"]."》"."\n".
			"👍点赞数：".$row["liked_count"];
	    }
	}

	$mysqli->close();
	return $musicArray;
  
}

function getAllMusic($table)
{
   
    $mysql_host =     "w.rdc.sae.sina.com.cn";//SAE_MYSQL_HOST_M;
    $mysql_user =       "w2w044wxx4"; //SAE_MYSQL_USER;
    $mysql_password =   "w5z14my332mklz33zjiki4yj34kx0mx5l20i11k4";    //SAE_MYSQL_PASS;
    $mysql_database =    "app_yujiage2";     // SAE_MYSQL_DB;
    
	
	$mysql_table = $table;
   
	$mysqli=new mysqli($mysql_host,$mysql_user,$mysql_password,$mysql_database);
	$con=mysqli_connect($mysql_host,$mysql_user,$mysql_password,$mysql_database);
	if($mysqli->connect_error){
	    echo "连接数据库失败：".$mysqli->connect_error;
	    $mysqli=null;
	    exit;
	}
	//echo "连接数据库成功！<br/>";

	// 执行查询并输出受影响的行数 
	mysqli_query($con,"SELECT * FROM `".$mysql_table."`");
	$num=mysqli_affected_rows($con);
	// echo $num;
	$musicArray="";
	for ($i=0; $i<$num; $i++) { 
		$ids=$i+1;
		$sql = "SELECT * FROM `".$mysql_table."` WHERE `id` = '".$ids."'";
		$result=$mysqli->query($sql);

		if ($result->num_rows > 0) {
		    while($row = $result->fetch_assoc()) {
		        // echo "name:".$row["music_name"]."<br>";
				$musicArray=$musicArray.
				$row["music_name"].",".
				$row["music_rank"].",".
				$row["music_url"].",";
				// echo $musicArray;
		    }
		}
	}

	$mysqli->close();
	return $musicArray;
  
}
function getPicUrl($randomBase){
	$picUrl = array(
		"http//cdn-img.easyicon.net/png/5801/580158.gif"
		,"http://cdn-img.easyicon.net/png/5801/580160.gif"
		,"http://cdn-img.easyicon.net/png/5801/580159.gif"
		,"http://cdn-img.easyicon.net/png/5801/580165.gif"
		,"http://cdn-img.easyicon.net/png/5801/580161.gif"
		,"http://cdn-img.easyicon.net/png/5801/580162.gif"
		,"http://cdn-img.easyicon.net/png/5801/580163.gif"
		,"http://cdn-img.easyicon.net/png/5801/580164.gif"
		,"http://cdn-img.easyicon.net/png/5800/580019.gif"
		,"http://cdn-img.easyicon.net/png/5800/580018.gif");
	return $picUrl[$randomBase%10];
}
?>