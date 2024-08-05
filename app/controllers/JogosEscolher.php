<?php 
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\database\models\Aluno;
use app\database\models\Turma;
use app\database\models\Usuario;

class JogosEscolher extends Base {
    private $user;
    private $validate;

    public function __construct()
    {
        $this->user = new Usuario;
        $this->validate = new Validate;
        $this->turma = new Turma;
        $this->aluno = new Aluno;
    }

    public function index($request, $response, $args) {
        $messages = Flash::getAll();

        $idAluno = $args['idAluno'];
        $aluno = $this->aluno->findBy('id', $idAluno);

        return $this->getTwig()->render($response, $this->setView('site/jogos'), [
            'title' => 'Escolha um Jogo',
            'messages' => $messages,
            'aluno' => $aluno
        ]);    
    }
}