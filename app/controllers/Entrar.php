<?php
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\database\models\Usuario;
use app\database\models\Professor;

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
            $user = $this->user->findBy('email', $email);
            $id = $user->id;
    
            // Inserir dados na tabela professor
            $this->professor = new Professor;
            $professorCreated = $this->professor->create(['usuario_id' => $id, 'nome' => $nome]);
    
            if ($professorCreated) {
                Flash::set('message', 'Cadastrado com sucesso');
                $_SESSION['user_logged_data'] = [
                    'nome' => $nome,
                    'email' => $email,
                    'id' => $id
                ];
                $_SESSION['is_logged_in'] = true;
                return \app\helpers\redirect($response, '/portal'); 
            } else {
                Flash::set('message', 'Ocorreu um erro ao cadastrar na tabela professor');
                return \app\helpers\redirect($response, '/cadastro');
            }
        }

        Flash::set('message', 'Ocorreu um erro ao cadastrar');
        return \app\helpers\redirect($response, '/cadastro');

    }
}