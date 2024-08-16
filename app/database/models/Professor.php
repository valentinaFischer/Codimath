<?php 
namespace app\database\models;

use PDOException;
use app\database\models\Connection;

class Professor extends Base
{
    protected $table = 'professor';

    public function countAll()
    {
        $connection = $this->connection;
        $stmt = $connection->query('SELECT COUNT(*) FROM professor');
        return $stmt->fetchColumn();
    }
}
