<?php
namespace app\database\models;

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
}