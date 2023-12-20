<?php

use \SRC\Http\Response;
use \SRC\Controller\Cadastro\ControllerCadastro;
use \Includes\Traccar\ApiTraccar;

// ---------------------------------------------------------------------- //
// GET

$obRouter->get('/',[
    function($request){
        return new Response(200, ControllerCadastro::index($request));
    }
]);

$obRouter->get('/rastreador/{imei}',[
    function($request, $imei){
        return new Response(200, ApiTraccar::checkDevice($imei));
    }
]);

$obRouter->get('/registro/{imei}',[
    function($request, $imei){
        return new Response(200, ControllerCadastro::registro($request, $imei));
    }
]);

$obRouter->post('/registro/{imei}',[
    function($request, $imei){
        return new Response(200, ControllerCadastro::setRegistro($request, $imei));
    }
]);