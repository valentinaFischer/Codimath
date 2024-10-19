<?php 
namespace app\controllers;

use app\classes\Validate;
use app\database\models\Aluno;
use app\database\models\Turma;
use app\database\models\Usuario;

class Perfil extends Base {
    private $user;
    private $validate;

    public function __construct() {
        $this->user = new Usuario;
        $this->validate = new Validate;
        $this->turma = new Turma;
        $this->aluno = new Aluno;
    }

    public function index($request, $response, $args) {

        $professorUsuarioId = $args['id'];
        $user = $this->user->findBy('id', $professorUsuarioId);

        $numeroTurmas = $this->turma->countByProfessor($professorUsuarioId);
        $numeroAlunos = $this->aluno->countAllByProfessor($professorUsuarioId);

        return $this->getTwig()->render($response, $this->setView('site/perfil'), [
            'title' => 'Perfil',
            'userId' => $professorUsuarioId,
            'numeroTurmas' => $numeroTurmas,
            'numeroAlunos' => $numeroAlunos,
            'user' => $user
        ]);    
    }
}