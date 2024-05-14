<?php
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\database\models\Usuario;

require_once __DIR__ . '/../helpers/redirect.php';

class Entrar extends Base
{

    public function __construct()
    {
        $this->validate = new Validate;
        $this->user = new Usuario;
    }


    public function create($request, $response, $args)
    {
        $messages = Flash::getAll();
        return $this->getTwig()->render($response, $this->setView('site/cadastro'), [
            'title' => 'Cadastro',
            'messages' => $messages
        ]);   
    }

    public function store($request, $response, $args)
    {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_STRING);

        $this->validate->required(['email', 'nome', 'senha'])->exist($this->user, 'email', $email);
        $errors = $this->validate->getErrors();

        if ($errors) {
            Flash::flashes($errors);
            return \app\helpers\redirect($response, '/cadastro');
        }

        $created = $this->user->create(['email' => $email, 'nome' => $nome, 'senha' => password_hash($senha, PASSWORD_DEFAULT)]);

        if ($created)
        {
            Flash::set('message', 'Cadastrado com sucesso');
            return \app\helpers\redirect($response, '/'); //enviar para página de painel do professor depois
        }

        Flash::set('message', 'Ocorreu um erro ao cadastrar');
        return \app\helpers\redirect($response, '/cadastro');

    }
}