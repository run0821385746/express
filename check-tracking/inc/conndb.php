<?php
	 try {
		 $obtion = array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8',);
		 $conn = new PDO('mysql:host=localhost;dbname=zkts_ktsdb', 'zkts_ktsdb', 't74NdFRv',$obtion);
		 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(Exception $e) {
		  exit('Unable to connect to database.');
	 }



// test
	 /*
	$sql = " SELECT * FROM users ";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	if($stmt->rowCount()>0){
		while($rs = $stmt->fetch()){
			echo $rs['name']."<br>";
		}
	}
*/