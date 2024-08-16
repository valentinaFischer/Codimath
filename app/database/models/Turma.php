<?php
namespace app\database\models;

use PDOException;
use app\traits\Connection;

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
}