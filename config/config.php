
<?php

$dns='mysql:host=localhost;dbname=all';
$user='root';
$password='';
$option=array(PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES utf8',);


	$con=new PDO($dns,$user,$password,$option);
	$con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	