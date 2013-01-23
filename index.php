<?php
require 'Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
// GET route
$app->get('/hello/:name/:name2', function ($name, $name2) {
    echo "Hello, $name, $name2";
});//test
// POST route
$app->post('/post', function () {
	$var1 = $app->request()->params('f1');
    echo "This is a POST route, $var1";
});
// PUT route
$app->put('/put', function () {
    echo 'This is a PUT route';
});

// DELETE route
$app->delete('/delete', function () {
    echo 'This is a DELETE route';
});
$app->run();