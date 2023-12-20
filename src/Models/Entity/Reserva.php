<?php

namespace SRC\Models\Entity;

use \SRC\Database\Database;
use \SRC\Database\Pagination;

use \SRC\Models\Entity\Veiculo;
use SRC\Sessions\Login as Session;
use \SRC\Models\Entity\Cliente;

class Reserva
{
    public static function find($unid)
    {
        $dbselect = (new Database('reservas'))->select("id=$unid")->fetchObject(self::class);

        if (!$dbselect)
            return false;

        $temp = json_decode($dbselect->jsondb);
        $temp->infomCliente->cadastro = empty($temp->infomCliente->cadastro) ? 'fisica' : $temp->infomCliente->cadastro;
        $temp->id = $dbselect->id;
        $temp->agencia = $dbselect->agencias;
        $temp->cliente = $dbselect->cliente;
        $temp->veiculo = $dbselect->veiculo;
        $temp->status = $dbselect->status;
        $temp->infomVeiculo->veiculo = (empty(Veiculo::findPlaca($temp->infomVeiculo->placa)->veiculo)? $temp->veiculo : Veiculo::findPlaca($temp->infomVeiculo->placa)->veiculo);        
        $temp->infomLocacao->calcao = (empty($temp->infomLocacao->calcao) ? '1000,00' : $temp->infomLocacao->calcao);
        $temp->infomLocacao->meiopagamento = (empty($temp->infomLocacao->meiopagamento) ? 'Credito' : $temp->infomLocacao->meiopagamento);
        $temp->infomLocacao->lavagem = (empty($temp->infomLocacao->lavagem) ? '60,00' : $temp->infomLocacao->lavagem);
        $temp->infomLocacao->statuslavagem = (empty($temp->infomLocacao->statuslavagem) ? 'Na Devolução' : $temp->infomLocacao->statuslavagem);

        return $temp;
    }

    //
    public static function checker($data)
    {
        $infomLocacao = $data->infomLocacao;

        $id = null;

        if (isset($data->id))
            $id = "id != $data->id AND";

        $dt_inicio = date('Y-m-d', strtotime($infomLocacao->dt_inicio));
        $dt_fim = date('Y-m-d', strtotime($infomLocacao->dt_fim));

        $listDatas = criarArrayDeDatas($dt_inicio, $dt_fim);

        $cont = count($listDatas);

        $query = "$id status = 0 AND veiculo = $infomLocacao->veiculo AND '$listDatas[0]' BETWEEN DATE(JSON_EXTRACT(jsondb, '$.infomLocacao.dt_inicio')) AND DATE(JSON_EXTRACT(jsondb, '$.infomLocacao.dt_fim'))";

        for ($i = 0; $i < $cont; $i++) {
            $query .= " OR $id status = 0 AND veiculo = $infomLocacao->veiculo AND '$listDatas[$i]' BETWEEN DATE(JSON_EXTRACT(jsondb, '$.infomLocacao.dt_inicio')) AND DATE(JSON_EXTRACT(jsondb, '$.infomLocacao.dt_fim'))";
        }

        $rowCount = (new Database("reservas"))->select($query)->rowCount();

        if ($rowCount != 0) {
            print '{"status":"error","alerta":"Já Exite Uma Reserva Para Esta Data"}';
            exit;
        }
    }

}