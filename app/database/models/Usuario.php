<?php
namespace app\database\models;

use PDO;
use PDOException;
use app\database\models\Connection;

class Usuario extends Base
{
    protected $table = 'usuario';

    public function countAll()
    {
        $connection = $this->connection;
        $stmt = $connection->query('SELECT COUNT(*) FROM usuario');
        return $stmt->fetchColumn();
    }

    public function deleteByProfessor($professorId) {
        $sql = "DELETE FROM usuario WHERE id IN (SELECT usuario_id FROM professor WHERE usuario_id = :professor_id)";
        
        $connection = $this->connection;
        $stmt = $connection->prepare($sql);
        $stmt->bindValue(':professor_id', $professorId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}