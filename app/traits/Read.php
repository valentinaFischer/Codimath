<?php
namespace app\traits;

use PDOException;

trait Read
{
    public function find($fetchAll = true)
    {
        try {
            $query = $this->connection->query("SELECT * FROM {$this->table}");
            return $fetchAll ? $query->fetchAll() : $query->fetch();
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }

    public function findBy($field, $value, $fetchAll = false)
    {
        try {
            $prepared = $this->connection->prepare("SELECT * FROM {$this->table} WHERE {$field} = :{$field}");
            $prepared->bindValue(":{$field}", $value);
            $prepared->execute();
            return $fetchAll ? $prepared->fetchAll() : $prepared->fetch();
        } catch (PDOException $e) {
            var_dump($e->getMessage());
        }
    }
}