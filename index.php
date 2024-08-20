<?php
 
session_start();
 
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use app\classes\TwigGlobal;
use Slim\Factory\AppFactory;
use Slim\Middleware\MethodOverrideMiddleware;
 
require __DIR__ . '/vendor/autoload.php';
 
 
$app = AppFactory::create();
 
TwigGlobal::set('is_logged_in', $_SESSION['is_logged_in'] ?? '');
TwigGlobal::set('user', $_SESSION['user_logged_data'] ?? '');
 
require __DIR__ . '/app/helpers/config.php';
require __DIR__ . '/app/middlewares/logged.php';
require __DIR__ . '/app/routes/user.php';
require __DIR__ . '/app/routes/site.php';
require __DIR__ . '/app/routes/entrar.php';
require __DIR__ . '/app/routes/aluno.php';
require __DIR__ . '/app/routes/jogos.php';
 
$methodOverrideMiddleware = new MethodOverrideMiddleware();
$app->add($methodOverrideMiddleware); //para poder trabalhar com PUT e DELETE, jÃ¡ que o HTML nÃ£o tem suporte para isso
 
$app->map(['GET', 'POST', 'DELETE', 'PATCH', 'PUT'], '/{routes:.+}', function ($request, $response) {
    $response->getBody()->write('Este endereÃ§o nÃ£o existe');
    return $response;
});
 
$app->run();