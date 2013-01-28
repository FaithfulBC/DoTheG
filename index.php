<?php
require 'dbconn.php';//db connect function
require 'Slim/Slim.php';//include slimframework

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->put('/upt_latest_date/:id','updateLatestDate');//update lastest date
$app->post('/add_User','addUser');//add User (Records are initialized 0)
$app->get('/dep_Rank/:id','getDepthRank');//dep_rank
$app->get('/score_Rank/:id','getScoreRank');//score_rank
$app->put('/upt_Record/:id','updateRecord');//update_record
$app->delete('/del_Rank:id', 'deleteRecord');//delete_record

$app->run();


?>