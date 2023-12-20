<?php

namespace SRC\Models\Entity;

use \SRC\Database\Database;
use \SRC\Database\Pagination;

use \SRC\Models\Entity\Veiculo;
use \SRC\Models\Entity\Reserva;

use SRC\Sessions\Login as Session;
use \SRC\Models\Entity\Cliente;

class Locacao
{
    public static function find($unid)
    {
        $dbselect = (new Database('locacoes'))->select("id=$unid")->fetchObject(self::class);

        if (!$dbselect)
            return false;

        $temp = json_decode($dbselect->jsondb);
        $temp->unid = $dbselect->unid;
        $temp->locacao = $dbselect->id;
        $temp->agencia = $dbselect->agencias;
        $temp->cliente = $dbselect->cliente;
        $temp->veiculo = (empty(Veiculo::findPlaca($temp->infomVeiculo->placa)->veiculo)? $temp->veiculo : Veiculo::findPlaca($temp->infomVeiculo->placa)->veiculo);
        $temp->status = $dbselect->status;
        $temp->processo = $dbselect->processo;
        $temp->dtcard = $dbselect->dtcard;
        $temp->infomVeiculo->veiculo = (empty(Veiculo::findPlaca($temp->infomVeiculo->placa)->veiculo)? $temp->veiculo : Veiculo::findPlaca($temp->infomVeiculo->placa)->veiculo);
        $temp->infomLocacao->processo = $dbselect->processo;
        $temp->infomLocacao->calcao = (empty($temp->infomLocacao->calcao) ? '1000,00' : $temp->infomLocacao->calcao);
        $temp->infomLocacao->meiopagamento = (empty($temp->infomLocacao->meiopagamento) ? 'Credito' : $temp->infomLocacao->meiopagamento);
        $temp->infomLocacao->lavagem = (empty($temp->infomLocacao->lavagem) ? '60,00' : $temp->infomLocacao->lavagem);
        $temp->infomLocacao->statuslavagem = (empty($temp->infomLocacao->statuslavagem) ? 'Na Devolução' : $temp->infomLocacao->statuslavagem);
        $temp->infomLocacao = self::recalcularDiarias($temp->infomLocacao);

        return $temp;
    }

    //
    public static function checker($data)
    {
        $infomLocacao = $data->infomLocacao;

        $id = null;

        if (isset($data->id))
            $id = "id != $data->id AND";

        $query = " $id status = 0 AND veiculo = $infomLocacao->veiculo AND '$infomLocacao->dt_inicio' BETWEEN DATE(JSON_EXTRACT(jsondb, '$.infomLocacao.dt_inicio')) AND DATE(JSON_EXTRACT(jsondb, '$.infomLocacao.dt_fim'))";

        return (new Database("locacoes"))->select($query)->rowCount();
    }

    //
    public static function setCard($data)
    {
        $infomLocacao = $data->infomLocacao;

        if ($infomLocacao->cliente == 0)
            $infomLocacao->cliente = Cliente::store($data);

        $infomVeiculo = Veiculo::find($infomLocacao->veiculo);

        if ($infomVeiculo->status != 0)
            return 2;

        $locacao = new \stdClass();
        $locacao->agencias = Session::find()->agencia;
        $locacao->cliente = $infomLocacao->cliente;
        $locacao->veiculo = $infomLocacao->veiculo;
        $locacao->status = 1;
        $locacao->jsondb = json_encode($data);

        (new Database("locacoes"))->insert((array) $locacao);

        $veiculo = new \stdClass();
        $veiculo->veiculo = $infomLocacao->veiculo;
        $veiculo->status = 1;

        Veiculo::setUpdate($veiculo);

        return true;
    }

    //
    public static function setEdit($data)
    {
        $find = self::find($data->locacao);

        $jsonUpdt = jsonUpdt($find, $data);

        if (isset($data->km))
            $jsonUpdt->km = $data->km;

        $locacao = new \stdClass();
        $locacao->cliente = $jsonUpdt->cliente;
        $locacao->veiculo = $jsonUpdt->veiculo;
        $locacao->status = $jsonUpdt->status;
        $locacao->processo = $jsonUpdt->processo;
        $locacao->jsondb = json_encode($jsonUpdt);

        (new Database('locacoes'))->update((array) $locacao, "id = $data->locacao");

        Veiculo::setUpdate($jsonUpdt);

        return true;
    }

    public static function updateID($unid)
    {
       $find = self::find($unid);
       $find->veiculo = (empty(Veiculo::findPlaca($find->infomVeiculo->placa)->veiculo)? $find->veiculo : Veiculo::findPlaca($find->infomVeiculo->placa)->veiculo);

       $agencia = Session::find()->agencia;

       return (new Database("locacoes"))->update(["veiculo" =>  $find->veiculo ], " agencias = $agencia AND id = $unid ");
    }

    // ------------------------------------------------------------------------------------------------- //
    private static function recalcularDiarias($temp)
    {
        date_default_timezone_set('America/Sao_Paulo');

        $hoje = date('Y-m-d H:i:s');

        $dt_inicil = $temp->dt_inicio;

        $dt_fim = $temp->dt_fim;

        $valor = formatMoeda($temp->vl_diaria);

        $desconto = formatMoeda($temp->vl_desconto);

        $dias = diasDatas($dt_inicil, $dt_fim);

        $adnu_diarias = diasDatas($dt_fim, $hoje);

        $vl_totaldiaria = ($dias * $valor);

        $temp->nu_diarias = $dias;

        $temp->vl_locacao = number_format($vl_totaldiaria, 2, ",", ".");

        if ($temp->processo == 0):
            if (strtotime($hoje) > strtotime($dt_fim)):
                $temp->nu_diarias = $dias;

                $temp->dt_inicio = $temp->dt_inicio;

                $temp->addt_fim = $hoje;

                $temp->adnu_diarias = $adnu_diarias;

                $temp->total_diarias = $dias + $adnu_diarias;

                $vl_totaldiaria = ($valor * $temp->total_diarias);

                $total = ($vl_totaldiaria - $desconto);

                $temp->vl_totaldiaria = number_format($total, 2, ",", ".");

            else:

                $temp->addt_fim = $dt_fim;

                $temp->adnu_diarias = 0;

                $temp->total_diarias = $dias;

                $total = ($vl_totaldiaria - $desconto);

                $temp->vl_totaldiaria = number_format($total, 2, ",", ".");
            endif;
        endif;

        //$temp->vl_totaldiaria = number_format($temp->vl_totaldiaria,2,",",".");        

        return $temp;
    }
}