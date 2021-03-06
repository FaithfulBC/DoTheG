<?php
require_once 'dbconn.php';//dbconnect function
require 'Slim/Slim.php';//include slimframework
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

//is there ID(user) in database
$app->get('/is_user/:id',function($id){
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
});

//update lastest date
$app->put('/upt_latest_date/:id',function($id){//isUser 요청 완료 후  updateLatestDate 요청하기
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
});

//add User (Records are initialized 0)
$app->post('/add_user',function()use($app){
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
});

//dep_rank
$app->get('/dep_rank/:id',function($id){
	$sql1 = "select * from ranking order by Depth desc limit 5";
	$sql2 = "select * from ranking where id=:id";
	$sql3 = "select count(*) from ranking where Depth>:Depth";
	try{
		$db = getConnection();
		$stmt = $db->query($sql1);//sql1 start
		$record1_5 = $stmt->fetchAll(PDO::FETCH_OBJ);
		$record1_5 = json_encode($record1_5);//배열화
		$record1_5 = json_decode($record1_5,true);//배열화 완료
		$stmt2 = $db->prepare($sql2);//sql2 start
		$stmt2->bindParam("id", $id);
		$stmt2->execute();
		$myRecord = $stmt2->fetchObject();
		$myRecord = json_encode($myRecord);//배열화
		$myRecord = json_decode($myRecord,true);//배열화 완료
		$Depth_record = $myRecord['Depth'];//깊이 값 가져오기
		$stmt3 = $db->prepare($sql3);//sql3 start
		$stmt3->bindParam("Depth", $Depth_record);
		$stmt3->execute();
		$myRank_temp = $stmt3->fetchObject();
		$myRank_temp = json_encode($myRank_temp);//배열화
		$myRank_temp = json_decode($myRank_temp,true);//배열화 완료
		$myRank = $myRank_temp['count(*)']+1;//랭크값
		$result = array('top5'=>$record1_5, 'myrank'=>$myRank, 'myrecord'=>$myRecord);//배열만들기
		echo json_encode($result);
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
});

//score_rank
$app->get('/score_rank/:id',function($id){
	$sql1 = "select * from ranking order by Score desc limit 5";
	$sql2 = "select * from ranking where id=:id";
	$sql3 = "select count(*) from ranking where Score>:Score";
	try{
		$db = getConnection();
		$stmt = $db->query($sql1);//sql1 start
		$record1_5 = $stmt->fetchAll(PDO::FETCH_OBJ);
		$record1_5 = json_encode($record1_5);//배열화
		$record1_5 = json_decode($record1_5,true);//배열화 완료
		$stmt2 = $db->prepare($sql2);//sql2 start
		$stmt2->bindParam("id", $id);
		$stmt2->execute();
		$myRecord = $stmt2->fetchObject();
		$myRecord = json_encode($myRecord);//배열화
		$myRecord = json_decode($myRecord,true);//배열화 완료
		$Score_record = $myRecord['Score'];//Score 값 가져오기
		$stmt3 = $db->prepare($sql3);//sql3 start
		$stmt3->bindParam("Score", $Score_record);
		$stmt3->execute();
		$myRank_temp = $stmt3->fetchObject();
		$myRank_temp = json_encode($myRank_temp);//배열화
		$myRank_temp = json_decode($myRank_temp,true);//배열화 완료
		$myRank = $myRank_temp['count(*)']+1;//랭크값
		$result = array('top5'=>$record1_5, 'myrank'=>$myRank, 'myrecord'=>$myRecord);//배열만들기
		echo json_encode($result);
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
});

//update_depth_record
$app->put('/upt_dep_record/:id',function($id)use($app){
	$request = $app->request();
	$body = $request->getBody();
	$record = json_decode($body);
	$sql = "update ranking set Depth=:Depth where id=:id";
	try{
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("Depth", $record->Depth);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($record);//or echo success data(for response ex.true)
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
});

//update_score_record
$app->put('/upt_score_record/:id',function($id)use($app){
	$request = $app->request();
	$body = $request->getBody();
	$record = json_decode($body);
	$sql = "update ranking set Score=:Score where id=:id";
	try{
		$db = getConnection();
		$stmt = $db->prepare($sql);
		$stmt->bindParam("Score", $record->Score);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		echo json_encode($record);//or echo success data(for response ex.true)
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
});

//delete_record
$app->delete('/del_rank/:id', function($id){
	$sql = "delete from ranking where id=:id";
	try{
		$db = getConnection();
		$stmt = $db ->prepare($sql);
		$stmt->bindParam("id", $id);
		$stmt->execute();
		$db = null;
		$message = array('result'=>'delete', 'id'=>$id);
		echo json_encode($message);
	}
	catch(PDOException $e){
		echo '{"error":{"text":'. $e->getMessage() .'}}';//throw error message(maybe json type)
	}
});

$app->run();


?>