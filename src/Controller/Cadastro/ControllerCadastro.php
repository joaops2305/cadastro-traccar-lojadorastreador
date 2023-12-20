<?php

namespace SRC\Controller\Cadastro;

use Includes\Traccar\ApiTraccar;
use SRC\Controller\Views;

class ControllerCadastro {
    public static function index($request) {
        return Views::renderForm("index", [
            "conteudo" => self::consulta($request),
        ]);
    }

    public static function consulta($request) {
        return Views::renderForm("Cadastro/consulta", []);
    }

    public static function registro($request, $imei) {
        $queryParams = $request->getQueryPrams();
        $modorestro = $queryParams['modo'] ?? null;

        $findDevice = json_decode(ApiTraccar::findDevice($imei), true);
        $findDevice[0]["marca"] = $findDevice[0]["attributes"]["marca"];

        $conteudo = Views::renderForm("Cadastro/rastreador", $findDevice[0]);

        $forcard = null;

        switch($modorestro) {
            case 'novoCliente':
                $forcard = Views::renderForm("Cadastro/novoCliente");
                break;

            case 'souCliente':
                $forcard = Views::renderForm("Cadastro/souCliente");
                break;

            default:
                $forcard = "
                <div class=\"box-buttons text-left\">
        <hr class=\"mt-3\">
        <button type=\"button\" class=\"btn btn-primary btn-sm\" onclick=\"location.href='?modo=souCliente';\">Já sou Cliente</button>
        <button type=\"button\" class=\"btn btn-primary btn-sm\" onclick=\"location.href='?modo=novoCliente';\">Não sou Cliente</button>
    </div>";
                break;
        }

        return Views::renderForm("index", [
            "conteudo" => $conteudo,
            "forcard" => $forcard
        ]);
    }

    public static function setRegistro($request, $imei) {
        $temp = json_decode(file_get_contents('php://input'));

        $queryParams = $request->getQueryPrams();
        $modorestro = $queryParams['modo'] ?? null;

        $reponse = null;

        switch($modorestro) {
            case 'novoCliente':
                $reponse = self::setNovoCliente($temp, $imei);
                break;

            case 'souCliente':
                $reponse = self::setSouCliente($temp, $imei);
                break;
        }

        return $reponse;
    }

    private static function setNovoCliente($temp, $imei) {
        $data = json_encode([
            "name" => $temp->name,
            "phone" => $temp->mobilePhone,
            "email" => $temp->email,
            "login" => $temp->cpf,
            "password" => NumerosCPF($temp->cpf, 6),
            "attributes" => [
                "resgitro" => true,
                "nivel" => 2,
                "informacoes" => $temp
            ]
        ]);

        $checkUser = ApiTraccar::checkUser($data);

        if($checkUser == 0) {
            $cardUser = json_decode(ApiTraccar::cardUser($data));

            $editDevice = json_decode(ApiTraccar::editDevice($imei));

            $permissions = ApiTraccar::permissions($cardUser->id, $editDevice->id);

            if($permissions->responseCode == 204)
                return '{"status":"sucesso"}';
        } else {
            return '{"status":"error","alerta":"Clente Já Cadatrado"}';
        }
    }

    private static function setSouCliente($temp, $imei) {
        $findUser = json_decode(ApiTraccar::findUser($temp));

        if($findUser != null) {
            $editDevice = json_decode(ApiTraccar::editDevice($imei));

            $permissions = ApiTraccar::permissions($findUser->id, $editDevice->id);

            if($permissions->responseCode == 204)
                return '{"status":"sucesso"}';
        } else {
            return '{"status":"error","alerta":"Clente Não Castrado"}';
        }
    }
}
