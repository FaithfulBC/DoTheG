<?php
    //dbconnect code
    //using PDO(PHP Data Object)
    function getConnection(){
    	$dbhost="127.0.0.1";//localhost
		$dbuser="root";
		$dbpass="1234";
		$dbname="dotheg_db";
		$dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $dbh;//when you close database connection, you should set $dbh = null
    }
?>