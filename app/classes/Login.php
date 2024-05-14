<?php
namespace app\classes;

use app\database\models\Usuario;

require_once __DIR__ . '/../helpers/redirect.php';

class Login 
{
    public function login($email, $senha)
    {
        $user = new Usuario;
        $userFound = $user->findBy('email', $email);

        if (!$userFound)
        {
            return false;
        }

        if (password_verify($senha, $userFound->senha))
        {
            $_SESSION['user_logged_data'] = [
                'nome' => $userFound->nome,
                'email' => $userFound->email
            ];
            $_SESSION['is_logged_in'] = true;
            return true;
        }

        return false;
    }

    public function logout()
    {
        unset($_SESSION['user_logged_data'], $_SESSION['is_logged_in']);
        session_destroy();
        return true;
    }
}