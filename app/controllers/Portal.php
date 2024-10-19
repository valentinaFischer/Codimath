<?php
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\database\models\Aluno;
use app\database\models\Usuario;
use app\database\models\Professor;

class Portal extends Base
{
    private $user;
    private $validate;

    public function __construct()
    {
        $this->user = new Usuario;
        $this->validate = new Validate;
        $this->professor = new Professor;
        $this->aluno = new Aluno;
    }

    public function index($request, $response) {
        $users = $this->user->find();

        $message = Flash::get('message');
        $professorUsuarioId = $_SESSION['user_logged_data']['id'];

        $professorCount = $this->professor->countAll();
        $alunoCount = $this->aluno->countAll();
        $userCount = $professorCount + $alunoCount;
        $user = $this->user->findBy('id', $professorUsuarioId);

        return $this->getTwig()->render($response, $this->setView('site/portal'), [
            'title' => 'Portal do Professor',
            'users' => $users,
            'message' => $message,
            'userCount' => $userCount,
            'professorCount' => $professorCount,
            'alunoCount' => $alunoCount,
            'userId' => $professorUsuarioId,
            'user' => $user
        ]);    
    }
}