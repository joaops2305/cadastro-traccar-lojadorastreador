<?php

namespace SRC\Models\Entity;

use SRC\Database\Database;

class Clientes
{
    public static function find($unid)
    {
        $dbSelect = (new Database('clientes'))->select("id=$unid")->fetchObject(self::class);

        if (!$dbSelect)
            return false;

        $temp = json_decode($dbSelect->jsondb);
        $temp->infomCliente->id = $dbSelect->id;

        $cliente = new \stdClass();
        $cliente->infomCliente = $temp->infomCliente;
        $cliente->infomCliente->cadastro = empty($temp->infomCliente->cadastro) ? null : $temp->infomCliente->cadastro;
        $cliente->Motoristas = $temp->Motoristas;

        return $cliente;
    }

    //
    public static function checkout($temp)
    {
        $sql = " JSON_EXTRACT(jsondb,'$.infomCliente.cpf') = '$temp->cpf' ";

        if (isset($temp->id))
            $sql = " id != $temp->id AND JSON_EXTRACT(jsondb,'$.infomCliente.cpf') = '$temp->cpf' ";

        $dbSelect = (new Database('clientes'))->select($sql);

        $rowCount = $dbSelect->rowCount();

        return $rowCount;
    }
}