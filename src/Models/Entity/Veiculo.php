<?php

namespace SRC\Models\Entity;

use \SRC\Database\Database;
use \SRC\Models\Entity\marcas_modelos as MacasModeles;

use SRC\Sessions\Login as Session;



class Veiculo
{
    public static function find($unid)
    {
        $dbSelect = (new Database('veiculos'))->select("id=$unid")->fetchObject(self::class);

        if (!$dbSelect)
            return false;

        $temp = json_decode($dbSelect->jsondb);
        $temp->veiculo = $dbSelect->id;
        $temp->deviceId = $dbSelect->deviceId;
        $temp->marca = is_numeric($temp->marca) ? MacasModeles::findFabricante($temp->marca)->Fabricante : $temp->marca;
        $temp->modelo = is_numeric($temp->modelo) ? MacasModeles::findModelo($temp->modelo)->modelo : $temp->modelo;
        $temp->status = $dbSelect->status;
        $temp->disabled = $dbSelect->disabled;

        return $temp;
    }

    //
    public static function findPlaca($placa)
    {
        $dbSelect = (new Database("veiculos"))->select("JSON_EXTRACT(jsondb,'$.placa') = '$placa'")->fetchObject(self::class);

        if (!$dbSelect)
            return null;

        $temp = json_decode($dbSelect->jsondb);
        $temp->veiculo = $dbSelect->id;

        return $temp;
    }


    //
    public static function checkout($temp)
    {
        $agencia = Session::find()->agencia;

        $select = null;

        if (isset($temp->veiculo))
            $select = " AND id !=" . $temp->veiculo;

        $dbSelect = (new Database('veiculos'))->select(" agencias = $agencia ".$select);

        $fetchAll = jsonList($dbSelect->fetchAll());

        $cont = 0;

        foreach ($fetchAll as $key => $obj) {
            $findata = self::find($obj->id);

            if ($findata->placa == $temp->placa):
                $cont++;
            endif;

            if ($findata->renavan == $temp->renavan):
                $cont++;
            endif;

            if ($findata->chassis == $temp->chassis):
                $cont++;
            endif;
        }

        return $cont;
    }

    //
    public static function setUpdate($data)
    {
        $find = Veiculo::find($data->veiculo);

        $jsonUpdt = jsonUpdt($find, $data);

        if (self::checkout($jsonUpdt) != 0)
            return false; 
            
        if($jsonUpdt->status == 6 || $jsonUpdt->status == 7)
             $jsonUpdt->status = 0;

        $data = json_encode($jsonUpdt);

        return (new Database('veiculos'))->update(["deviceId" => $jsonUpdt->deviceId, "jsondb" => $data, "status" => $jsonUpdt->status, "disabled" => $jsonUpdt->disabled ], " id = $jsonUpdt->veiculo ");
    }
}