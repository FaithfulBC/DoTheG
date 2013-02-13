<?php
require_once 'dbconn.php';//dbconnect function
require 'Slim/Slim.php';//include slimframework

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->get('/isUser/:id','isUser');//is there ID(user) in database
$app->put('/upt_latest_date/:id','updateLatestDate');//update lastest date
$app->post('/add_User','addUser');//add User (Records are initialized 0)
$app->get('/dep_Rank/:id','getDepthRank');//dep_rank
$app->get('/score_Rank/:id','getScoreRank');//score_rank
$app->put('/upt_Record/:id','updateRecord');//update_record
$app->delete('/del_Rank:id', 'deleteRecord');//delete_record
$app->get('/test/:id', 'test_f');//db connection test

$app->run();
function test_f($id){
	$sql = "select * from ranking";
	try{
		$db = getConnection();
		$stmt = $db->query($sql);
		$record = $stmt->fetchAll(PDO::FETCH_OBJ);
		$db = null;
		echo json_encode($record);
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
}
function isUser($id){//complete
	$sql = "select count(*) from ranking where id=:id";
	$sql1 = "select * from ranking where id=:id";
	try{
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$count = $stmt->fetchObject();
		$count = json_encode($count);//stdClass object를 array로 변형하는 과정
		$count = json_decode($count,true);//변형 끝
		if($count['count(*)'] == 0){
			$db = null;
			$message = array('user'=>'empty','mode'=>'sign_up_user');
			echo json_encode($message);//go to sign up page
		}
		else if($count['count(*)'] == 1){//count == 1
			$stmt2 = $db->prepare($sql1);
			$stmt2->bindParam("id", $id);
			$stmt2->execute();
			$record = $stmt2->fetchObject();
			$db = null;
			echo json_encode($record);//update local storage record
		}
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
}
function updateLatestDate($id){//isUser 요청 완료 후  updateLatestDate 요청하기
	$sql = "update ranking set latest_date=now() where id=:id";
	$sql2 = "select latest_date from ranking where id=:id";
	try{
		$db = getConnection();//db connection
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $id);
		$stmt->execute();//query execute
		$stmt2 = $db->prepare($sql2);
		$stmt2->bindParam("id", $id);
		$stmt2->execute();
		$time = $stmt2->fetchObject();
		$db = null;//db disconnection
		echo json_encode($time);//echo success data(time) if invalid id data = 'false'
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
}
function addUser(){
	$request = $app->request();
	$body = $request->getBody();
	$record = json_decode($body);
	$sql = "insert into ranking (id, name, Dev_num, Depth, Score, latest_date)values(:id, :name, :Dev_num, 0, 0, now())";
	try{
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $record->id);
		$stmt->bindParam("name", $record->name);
		$stmt->bindParam("Dev_num", $record->Dev_num);
		$stmt->execute();
		$db = null;
		echo json_encode($record);//echo success data(for response ex.true)
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
}
function getDepthRank($id){//my rank함수를 따로 만들까?
	$sql1 = "select * from ranking order by Depth desc limit 5";
	$sql2 = "select * from ranking where id=:id";
	$sql3 = "select count(*) from ranking where Depth>:Depth";
	try{
		$db = getConnection();
		$stmt = $db->query($sql1);
		//미완성
		
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
}
function getScoreRank($id){
	
	try{
		$db = getConnection();
		
		
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
}
function updateRecord(){
	$request = $app->request();
	$body = $request->getBody();
	$record = json_decode($body);
	$sql = "update ranking set Depth=:Depth, Score=:Score where id=:id";
	try{
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("Depth", $record->Depth);
		$stmt->bindParam("Score", $record->Score);
		$stmt->bindParam("id", $record->id);
		$stmt->execute();
		$db = null;
		echo json_encode($record);//or echo success data(for response ex.true)
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
}
function deleteRecord($id){
	
}
?>