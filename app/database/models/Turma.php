<?php
namespace app\database\models;

use PDO;
use PDOException;
use app\traits\Connection;
use app\database\models\Aluno;

class Turma extends Base
{
    use Connection;
    protected $table = 'turma';

    public function findByProfessor($professorUsuarioId) {
        $sql = "SELECT * FROM turma WHERE professor_usuario_id = :professor_usuario_id";
        $connection = $this->connection;
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':professor_usuario_id', $professorUsuarioId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countByProfessor($professorUsuarioId) {
        $sql = "SELECT COUNT(*) FROM turma WHERE professor_usuario_id = :professor_usuario_id";
        $connection = $this->connection;
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':professor_usuario_id', $professorUsuarioId);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function deleteByProfessor($professorId) {
        $connection = $this->connection;

        // obtém todas as turmas associadas ao professor
        $turmas = $this->findByProfessor($professorId);
        $this->aluno = new Aluno;
        // Remove todos os alunos de cada turma
        foreach ($turmas as $turma) {
            $this->aluno->deleteByTurma($turma->id);
        }
        // Lógica para excluir todas as turmas associadas ao professor
        $stmt = $connection->prepare("DELETE FROM turma WHERE professor_usuario_id = :professorId");
        $stmt->bindParam(':professorId', $professorId, PDO::PARAM_INT);
      
        return $stmt->execute();
    }
}