<?php 
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\database\models\Aluno;
use app\database\models\Usuario;
use app\database\models\Turma as TurmaModels;

class Turma extends Base {
    private $user;
    private $validate;

    public function __construct()
    {
        $this->user = new Usuario;
        $this->validate = new Validate;
        $this->turma = new TurmaModels;
        $this->aluno = new Aluno;
    }

    public function index($request, $response, $args) {
        $users = $this->user->find();

        $messages = Flash::getAll();

        $professorUsuarioId = $_SESSION['user_logged_data']['id'];

        $id = $args['id'];

        $turma = $this->turma->findBy('id', $id);

        return $this->getTwig()->render($response, $this->setView('site/turma_edit'), [
            'title' => 'Editar Turma',
            'users' => $users,
            'messages' => $messages,
            'turma' => $turma
        ]);    
    }

    public function update($request, $response, $args) {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $id = $args['id'];
    
        $this->validate->required(['nome']);
        $errors = $this->validate->getErrors();
    
        if ($errors) {
            Flash::flashes($errors);
            return \app\helpers\redirect($response, "/turma/edit/{$id}");
        }
    
        $updated = $this->turma->update([
            'fields' => ['nome' => $nome],
            'where' => ['id' => $id]
        ]);
    
        if ($updated) {
            Flash::set('message', 'Atualizado com sucesso');
            return \app\helpers\redirect($response, "/criarTurma");
        } else {
            Flash::set('message', 'Ocorreu um erro ao atualizar a turma');
            return \app\helpers\redirect($response, "/turma/edit/{$id}");
        }
    }    

    public function destroy($request, $response, $args)
    {
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        // Verificar se a turma existe
        $turma = $this->turma->findBy('id', $id);

        if (!$turma) {
            Flash::set('message', 'Turma não encontrada', 'danger');
            return \app\helpers\redirect($response, '/criarTurma');
        }

        $alunos = $this->aluno->findBy('turma_id', $id);
        if (!empty($alunos)) {
            Flash::set('message', 'Não é possível deletar a turma, pois existem alunos relacionados', 'danger');
            return \app\helpers\redirect($response, '/criarTurma');
        }

        $deleted = $this->turma->delete('id', $id);

        if ($deleted) {
            Flash::set('message', 'Deletada com sucesso!');
        } else {
            Flash::set('message', 'Não foi possível deletar essa turma', 'danger');
        }

        return \app\helpers\redirect($response, '/criarTurma');
    }
}