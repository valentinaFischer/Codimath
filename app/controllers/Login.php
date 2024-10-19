<?php
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\classes\Login as Loggin;
use app\database\models\Usuario;

require_once __DIR__ . '/../helpers/redirect.php';

class Login extends Base
{

    private $login;

    public function __construct()
    {
        $this->login = new Loggin;
        // Verifica se o usuário tem o cookie "remember_me"
        if (!isset($_SESSION['is_logged_in']) && isset($_COOKIE['remember_me'])) {
            $email = $_COOKIE['remember_me'];
            $user = new Usuario;
            $userFound = $user->findBy('email', $email);

            if ($userFound) {
                // Autenticar o usuário automaticamente
                $_SESSION['user_logged_data'] = [
                    'nome' => $userFound->nome,
                    'email' => $userFound->email,
                    'id' => $userFound->id
                ];
                $_SESSION['is_logged_in'] = true;
            }
        }
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
        $rememberMe = filter_input(INPUT_POST, 'remember_me', FILTER_SANITIZE_STRING);

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
            if ($rememberMe) {
                // Cria um cookie seguro para lembrar o login
                setcookie('remember_me', $email, time() + (86400 * 30), "/", "", true, true); // 30 dias de validade
            }
            return \app\helpers\redirect($response, '/portal');
        }

        Flash::set('message', 'Não foi possível efetuar o Login, tente novamente', 'danger');
        return \app\helpers\redirect($response, '/login');
   }

   public function destroy($request, $response)
   {
        $this->login->logout();

        // Apagar o cookie "remember_me"
        setcookie('remember_me', '', time() - 3600, "/");

        return \app\helpers\redirect($response, '/');
   }
}