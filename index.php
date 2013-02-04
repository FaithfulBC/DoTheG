<?php
require 'dbconn.php';//db connect function
require 'Slim/Slim.php';//include slimframework

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
//(is User)function is not required. it will be solved in the local storage.
$app->put('/upt_latest_date/:id','updateLatestDate');//update lastest date
$app->post('/add_User','addUser');//add User (Records are initialized 0)
$app->get('/dep_Rank/:id','getDepthRank');//dep_rank
$app->get('/score_Rank/:id','getScoreRank');//score_rank
$app->put('/upt_Record/:id','updateRecord');//update_record
$app->delete('/del_Rank:id', 'deleteRecord');//delete_record

$app->run();

function updateLatestDate($id){
	$sql = "update ranking set latest_date=now() where id=:id";
	try{
		$db = getConnection();//db connection
		$stmt = $db->prepare($sql);
		$stmt->bindParam("id", $id);
		$stmt->execute();//query execute
		$db = null;//db disconnection
		//echo success data(for response ex. true)
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
		//echo success data(for response ex.true)
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
		
		
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
}
?>