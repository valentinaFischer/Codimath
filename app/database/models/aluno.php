<?php 
namespace app\database\models;

use PDO;
use app\traits\Connection;

class Aluno extends Base {
    protected $table = 'aluno';

    use Connection;

    public function findAllWithTurmaByProfessor($professorUsuarioId) {
        $sql = "SELECT aluno.*, turma.nome as turma_nome
                FROM aluno
                INNER JOIN turma ON aluno.turma_id = turma.id
                WHERE turma.professor_usuario_id = :professor_usuario_id";
        $connection = $this->connection;
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':professor_usuario_id', $professorUsuarioId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findAllByTurma($idTurma) {
        $sql = "SELECT * FROM aluno WHERE turma_id = :idTurma";
    
        $connection = $this->connection;
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':idTurma', $idTurma, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll()
    {
        $connection = $this->connection;
        $stmt = $connection->query('SELECT COUNT(*) FROM aluno');
        return $stmt->fetchColumn();
    }

    public function findDadosJogo($idAluno, $jogo) {
        $sql = "SELECT * FROM $jogo WHERE id_aluno = :id_aluno";
    
        $connection = $this->connection;
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':id_aluno', $idAluno, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}