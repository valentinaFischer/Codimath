<?php
namespace app\controllers;

use app\classes\Flash;
use app\classes\Validate;
use app\database\models\Usuario;

//Para editar ou deletar o usuário

require_once __DIR__ . '/../helpers/redirect.php';

class User extends Base
{
    public function __construct()
    {
        $this->validate = new Validate;
        $this->user = new Usuario;
    }

    public function edit($request, $response, $args)
    {
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        $user = $this->user->findBy('id', $id);

        if (!$user)
        {
            Flash::set('message', 'Usuário não encontrado', 'danger');
            return \app\helpers\redirect($response, '/');
        }

        $messages = Flash::getAll();

        return $this->getTwig()->render($response, $this->setView('site/user_edit'), [
            'title' => 'Editar Conta',
            'user' => $user,
            'messages' => $messages
        ]);    
    }

    public function update($request, $response, $args)
    {
        $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_STRING);
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        $this->validate->required(['email', 'nome']);
        $errors = $this->validate->getErrors();

        if ($errors) {
            Flash::flashes($errors);
            return \app\helpers\redirect($response, '/user/edit/' . $id);
        }

        $updated = $this->user->update(['fields' => ['nome' => $nome, 'email' => $email], 'where' => ['id' => $id]]);

        if ($updated)
        {
            Flash::set('message', 'Atualizado com sucesso!');
            return \app\helpers\redirect($response, '/user/edit/' . $id);
        }

        Flash::set('message', 'Não foi possível atualizar', 'danger');
        return \app\helpers\redirect($response, '/user/edit/' . $id);

    }

    public function destroy($request, $response, $args)
    {
        $id = filter_var($args['id'], FILTER_SANITIZE_NUMBER_INT);

        $user = $this->user->findBy('id', $id);

        if (!$user)
        {
            Flash::set('message', 'Usuário não encontrado', 'danger');
            return \app\helpers\redirect($response, '/');
        }

        $deleted = $this->user->delete('id', $id);

        if ($deleted)
        {
            Flash::set('message', 'Deletado com sucesso!');
            return \app\helpers\redirect($response, '/');
        }

        Flash::set('message', 'Não foi possível deletar sua conta', 'danger');
        return \app\helpers\redirect($response, '/');
   }
}