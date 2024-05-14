<?php
namespace app\routes;

use app\controllers\User;
use app\controllers\Login;

$app->get('/user/edit/{id}', User::class . ":edit");
$app->put('/user/update/{id}', User::class . ":update");
$app->delete('/user/delete/{id}', User::class . ":destroy");
$app->get('/login', Login::class . ":index");
$app->post('/login', Login::class . ":store");
$app->get('/logout', Login::class . ":destroy");
