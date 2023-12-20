<?php

namespace SRC\Models\Entity;

use \SRC\Database\Database;
use \SRC\Database\Pagination;

use \SRC\Models\Entity\Veiculo;
use \SRC\Models\Entity\Locacao;

use SRC\Sessions\Login as Session;

class Vistoria
{
    //
    public static function find($unid)
    {
        $dbselect = (new Database("vistorias"))->select("id = $unid")->fetchObject(self::class);

        $vistoria = json_decode($dbselect->jsondb);
        $vistoria->vistoria = $dbselect->id;
        $vistoria->agencia = $dbselect->agencias;
        $vistoria->locacao = $dbselect->locacao;
        $vistoria->veiculo = (empty(Veiculo::findPlaca($vistoria->infomVeiculo->placa)->veiculo) ?: 'ok');
        $vistoria->status = $dbselect->status;
        $vistoria->dtcard = $dbselect->dtcard;
        $vistoria->informe = empty($vistoria->informe) ? null : $vistoria->informe;
        $vistoria->kmvistoria = empty($vistoria->kmvistoria) ? null : $vistoria->kmvistoria;
        $vistoria->infomVeiculo->id = (empty(Veiculo::findPlaca($vistoria->infomVeiculo->placa)->veiculo) ? $vistoria->infomVeiculo->id : Veiculo::findPlaca($vistoria->infomVeiculo->placa)->veiculo);
        $vistoria->infomVeiculo->placa = (empty(Veiculo::findPlaca($vistoria->infomVeiculo->placa)->veiculo) ? 'ok' : Veiculo::findPlaca($vistoria->infomVeiculo->placa)->placa);

        return $vistoria;
    }
    
    //
    public static function checker($unid)
    {
        return (new Database("vistorias"))->select("locacao = $unid")->rowCount();
    }

    //
    public static function setCard($data)
    {
        $Locacao = Locacao::find($data->locacao);
        $veiculo = Veiculo::find($Locacao->veiculo);

        $data->veiculo = $Locacao->veiculo;
        $data->infomVeiculo = $veiculo;
        $data->chekelist = [];

        $vistoria = new \stdClass();
        $vistoria->agencias = Session::find()->agencia;
        $vistoria->type = $data->type;
        $vistoria->locacao = $data->locacao;
        $vistoria->veiculo = $data->veiculo;
        $vistoria->jsondb = json_encode($data);

        (new Database('vistorias'))->insert((array) $vistoria);

        $locacao = new \stdClass();
        $locacao->locacao = $data->locacao;
        $locacao->status = $data->type;

        Locacao::setEdit($locacao);

        return true;
    }

    //
    public static function setUpdate($data)
    {
        $find = self::find($data->vistoria);
        $jsonUpdt = jsonUpdt($find, $data);

        $status = $jsonUpdt->type;

        if ($jsonUpdt->status == 1):
            switch ($jsonUpdt->type) {
                case 3:
                    $status = 6;
                    break;

                default:
                    $status = 4;
                    break;
            }
        endif;

        $data = json_encode($jsonUpdt);

        (new Database("vistorias"))->update(["jsondb" => $data, "status" => $jsonUpdt->status], " id = $jsonUpdt->vistoria ");

        $locacao = new \stdClass();
        $locacao->locacao = $jsonUpdt->locacao;
        $locacao->status = $status;
        $locacao->km = $jsonUpdt->kmvistoria;

        Locacao::setEdit($locacao);

        return true;
    }

    public static function updateID($idAntigo, $idNovo)
    {
       $agencia = Session::find()->agencia;
       return (new Database("vistorias"))->update(["locacao" => $idNovo ], " agencias = $agencia AND locacao = $idAntigo ");
    }
}