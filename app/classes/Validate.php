<?php
namespace app\classes;

class Validate
{
    private $errors = [];

    public function required(array $fields)
    {
        foreach ($fields as $field) {
            if (empty($_POST[$field])) {
                $this->errors[$field] = "Todos os campos são obrigatórios";
            }
        }

        return $this;
    }

    public function exist($model, $field, $value)
    {
        $data = $model->findBy($field, $value);

        if ($data)
        {
            $this->errors[$field] = "Já cadastrado";
        }

        return $this;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}