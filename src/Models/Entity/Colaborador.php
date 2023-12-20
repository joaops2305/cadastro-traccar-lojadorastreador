<?php

namespace SRC\Models\Entity;

use SRC\Database\Database;

class Colaborador
{
    //
    public static function find($id)
    {
        $dbSelect = (new Database('colaboradores'))->select("id=$id")->fetchObject(self::class);

        $temp = null;

        if ($dbSelect) {
            $temp = json_decode($dbSelect->jsondb);
            $temp->id = $id;

            $temp->cpf = formatCnpjCpf($temp->cpf);
            $temp->phone = formatPhone($temp->phone);
            $temp->mobilePhone = formatPhone($temp->mobilePhone);            
        }

        return $temp;
    }

    //
    public static function checkout($temp)
    {
        $sql = " JSON_EXTRACT(jsondb,'$.cpf') = '$temp->cpf' OR JSON_EXTRACT(jsondb,'$.rg') = '$temp->rg' OR JSON_EXTRACT(jsondb,'$.email') = '$temp->email' ";

        if (isset($temp->id))
            $sql = " id != $temp->id AND JSON_EXTRACT(jsondb,'$.cpf') = '$temp->cpf' OR id != $temp->id AND JSON_EXTRACT(jsondb,'$.rg') = '$temp->rg' OR id != $temp->id AND JSON_EXTRACT(jsondb,'$.email') = '$temp->email' ";

        $dbSelect = (new Database('colaboradores'))->select($sql);

        $rowCount = $dbSelect->rowCount();

        return $rowCount;
    }
}
