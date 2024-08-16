<?php
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\database\models\Turma;
use app\database\models\Usuario;
use app\database\models\Aluno as AlunoModels;

class Aluno extends Base {
    private $user;
    private $validate;

    public function __construct()
    {
        $this->user = new Usuario;
        $this->validate = new Validate;
        $this->turma = new Turma;
        $this->aluno = new AlunoModels;
    }

    public function index($request, $response, $args) {
        $users = $this->user->find();

        $messages = Flash::getAll();

        $professorUsuarioId = $_SESSION['user_logged_data']['id'];
        $turmas = $this->turma->findByProfessor($professorUsuarioId);

        $id = $args['id'];

        $aluno = $this->aluno->findBy('id', $id);

        return $this->getTwig()->render($response, $this->setView('site/aluno_edit'), [
            'title' => 'Editar Aluno',
            'users' => $users,
            'messages' => $messages,
            'turmas' => $turmas,
            'aluno' => $aluno
        ]);    
    }

    public function update($request, $response, $args) {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $turma = filter_input(INPUT_POST, 'turmaSelecionada', FILTER_SANITIZE_NUMBER_INT);
        $id = $args['id'];
    
        $this->validate->required(['nome', 'turmaSelecionada']);
        $errors = $this->validate->getErrors();
    
        if ($errors) {
            Flash::flashes($errors);
            return \app\helpers\redirect($response, "/aluno/edit/{$id}");
        }
    
        $updated = $this->aluno->update([
            'fields' => ['nome' => $nome, 'turma_id' => $turma],
            'where' => ['id' => $id]
        ]);
    
        if ($updated) {
            Flash::set('message', 'Atualizado com sucesso');
            return \app\helpers\redirect($response, "/criarTurma");
        } else {
            Flash::set('message', 'Ocorreu um erro ao atualizar o aluno');
            return \app\helpers\redirect($response, "/aluno/edit/{$id}");
        }
    }    

    public function destroy($request, $response, $args)
    {
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        $aluno = $this->aluno->findBy('id', $id);

        if (!$aluno)
        {
            Flash::set('message', 'Aluno não encontrado', 'danger');
            return \app\helpers\redirect($response, '/criarTurma');
        }

        $deleted = $this->aluno->delete('id', $id);

        if ($deleted)
        {
            Flash::set('message', 'Deletado com sucesso!');
            return \app\helpers\redirect($response, '/criarTurma');
        }

        Flash::set('message', 'Não foi possível deletar esse aluno', 'danger');
        return \app\helpers\redirect($response, '/criarTurma');
   }
}