<?php
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\database\models\Turma;
use app\database\models\Usuario;

class Gerencie extends Base
{
    private $user;
    private $validate;

    public function __construct()
    {
        $this->user = new Usuario;
        $this->validate = new Validate;
        $this->turma = new Turma;
    }

    public function index($request, $response) {
        $message = Flash::get('message');

        $professorUsuarioId = $_SESSION['user_logged_data']['id'];
        $turmas = $this->turma->findByProfessor($professorUsuarioId);

        return $this->getTwig()->render($response, $this->setView('site/gerencie'), [
            'title' => 'Gerencie as Turmas',
            'turmas' => $turmas,
            'idProfessor' => $professorUsuarioId
        ]);    
    }
}