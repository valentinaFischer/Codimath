<?php 
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\database\models\Aluno;
use app\database\models\Turma;
use app\database\models\Usuario;


class CriarTurma extends Base {
    private $user;
    private $validate;

    public function __construct()
    {
        $this->user = new Usuario;
        $this->validate = new Validate;
        $this->turma = new Turma;
        $this->aluno = new Aluno;
    }

    public function index($request, $response) {
        $users = $this->user->find();

        $professorUsuarioId = $_SESSION['user_logged_data']['id'];

        $turmas = $this->turma->findByProfessor($professorUsuarioId);
        $alunos = $this->aluno->findAllWithTurmaByProfessor($professorUsuarioId);

        $messages = Flash::getAll();

        return $this->getTwig()->render($response, $this->setView('site/criar_turma'), [
            'title' => 'Configurar Turmas',
            'users' => $users,
            'messages' => $messages,
            'turmas' => $turmas,
            'alunos' => $alunos,
            'userId' => $professorUsuarioId
        ]);    
    }

    public function store($request, $response) {
        $nomeTurma = filter_input(INPUT_POST, 'nomeTurma', FILTER_SANITIZE_STRING);

        $this->validate->required(['nomeTurma']);
        $errors = $this->validate->getErrors();

        if ($errors) {
            Flash::flashes($errors);
            return \app\helpers\redirect($response, '/criarTurma');
        }

        // Obter o ID do usuário logado
        $professorUsuarioId = $_SESSION['user_logged_data']['id'];

        // Criar uma nova turma
        
        $created = $this->turma->create([
            'nome' => $nomeTurma,
            'professor_usuario_id' => $professorUsuarioId
        ]);

        if ($created) {
            Flash::set('message', 'Turma criada com sucesso');
            return \app\helpers\redirect($response, '/criarTurma');
        }

        Flash::set('message', 'Ocorreu um erro ao criar a turma');
        return \app\helpers\redirect($response, '/criarTurma');
    }

    public function adicionarAluno($request, $response) {
        $nomeAluno = filter_input(INPUT_POST, 'nomeAluno', FILTER_SANITIZE_STRING);
        $turmaId = filter_input(INPUT_POST, 'turmaSelecionada', FILTER_SANITIZE_NUMBER_INT);

        $this->validate->required(['nomeAluno']);
        $errors = $this->validate->getErrors();

        if ($errors) {
            Flash::flashes($errors);
            return \app\helpers\redirect($response, '/criarTurma');
        }

        $created = $this->aluno->create([
            'nome' => $nomeAluno,
            'turma_id' => $turmaId
        ]);
    
        if ($created) {
            Flash::set('message', 'Aluno adicionado com sucesso', 'success');
            return \app\helpers\redirect($response, '/criarTurma');
        }
    
        Flash::set('message', 'Ocorreu um erro ao adicionar o aluno', 'danger');
        return \app\helpers\redirect($response, '/criarTurma');
    }
}