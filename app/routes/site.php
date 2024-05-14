<?php
namespace app\routes;

use app\controllers\Home;
use app\controllers\Portal;

$app->get('/', Home::class . ":index");
$app->get('/portal', Portal::class . ":index")->add($logged);