<?php

namespace SRC\Models\Entity;

use \SRC\Database\Database;

class Vistorias
{
    //
    public static function find($unid)
    {
        $dbselect = (new Database("vistorias"))->select("id = $unid")->fetchObject(self::class);

        $vistoria = json_decode($dbselect->jsondb);
        $vistoria->vistoria = $dbselect->id;
        $vistoria->locacao = $dbselect->locacao;
        $vistoria->veiculo = $dbselect->veiculo;
        $vistoria->status = $dbselect->status;
        $vistoria->informe = empty($vistoria->informe) ? null : $vistoria->informe;
        $vistoria->kmvistoria = empty($vistoria->kmvistoria) ? null : $vistoria->kmvistoria;

        return $vistoria;
    }

    //
    public static function checker($unid)
    {
        return (new Database("vistorias"))->select("locacao = $unid")->rowCount();
    }
}