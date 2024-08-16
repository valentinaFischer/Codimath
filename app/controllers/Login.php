<?php
namespace app\controllers;

use app\classes\Flash;
use app\classes\Login as Loggin;
use app\classes\Validate;

require_once __DIR__ . '/../helpers/redirect.php';

class Login extends Base
{

    private $login;

    public function __construct()
    {
        $this->login = new Loggin;
    }

    public function index($request, $response)
   {
        $messages = Flash::getAll();
        return $this->getTwig()->render($response, $this->setView('site/login'), [
            'title' => 'Login',
            'messages' => $messages
        ]);    
   }

   public function store($request, $response)
   {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

        $validate = new Validate;
        $validate->required(['email', 'senha']);
        $errors = $validate->getErrors();

        if ($errors)
        {
            Flash::flashes($errors);
            return \app\helpers\redirect($response, '/login');
        }

        $logged = $this->login->login($email, $senha);

        if ($logged)
        {
            return \app\helpers\redirect($response, '/portal');
        }

        Flash::set('message', 'Não foi possível efetuar o Login, tente novamente', 'danger');
        return \app\helpers\redirect($response, '/login');
   }

   public function destroy($request, $response)
   {
        $this->login->logout();

        return \app\helpers\redirect($response, '/');
   }
}