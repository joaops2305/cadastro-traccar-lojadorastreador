<?php

namespace SRC\Models\Entity;

use SRC\Database\Database;
use SRC\Sessions\Login as Session;

class Cliente
{
    //
    public static function find($unid)
    {
        $dbSelect = (new Database('clientes'))->select("id=$unid")->fetchObject(self::class);

        if (!$dbSelect)
            return false;

        $temp = json_decode($dbSelect->jsondb);
        $temp->infomCliente->cliente = $dbSelect->id;

        $cliente = new \stdClass();
        $cliente->infomCliente = $temp->infomCliente;
        $cliente->infomCliente->cadastro = empty($temp->infomCliente->cadastro) ? null : $temp->infomCliente->cadastro;
        $cliente->infomCliente->phone = empty($temp->infomCliente->phone) ? null : $temp->infomCliente->mobilePhone;
        $cliente->Motoristas = $temp->Motoristas;

        return $cliente;
    }

    //
    public static function checkout($data)
    {
        $agencia = Session::find()->agencia;

        $sql = " agencias = $agencia AND JSON_EXTRACT(jsondb,'$.infomCliente.cpf') = '$data->cpf' ";

        if (isset($data->cliente))
            $sql = " id != $data->cliente AND JSON_EXTRACT(jsondb,'$.infomCliente.cpf') = '$data->cpf' ";

        $rowCount = (new Database('clientes'))->select($sql)->rowCount();

        if($rowCount != 0){
            print '{"status":"error","alerta":"Clientes Já Cadastrado"}'; exit;
        }
    }

    //
    public static function store($data)
    {
        self::checkout($data->infomCliente);
        
        $cliente = new \stdClass();
        $cliente->infomCliente = $data->infomCliente;
        $cliente->Motoristas[] = (empty($data->Motoristas)) ? $data->infomMotorista : $data->Motoristas;
        $cliente = json_encode($cliente); 

        $agencia = Session::find()->agencia;
        
        return (new Database('clientes'))->insert(['agencias' => $agencia ,'jsondb' => $cliente]);
    }

    //
    public static function update($data, $id)
    {
        $find = self::find($id);
        $Motoristas = (array) $find->Motoristas;

        $jsonUpdt = jsonUpdt($find, $data);

        upArray($Motoristas, $jsonUpdt->Motoristas);

        $jsonUpdt->Motoristas = $Motoristas;

        if (Cliente::checkout($jsonUpdt->infomCliente))
            return false;

        $data = json_encode($jsonUpdt);

        return (new Database('clientes'))->update(['jsondb' => $data], 'id=' . $id);
    }

    //
    public static function delete($unid)
    {
        return (new Database('clientes'))->delete('id=' . $unid);
    }

}