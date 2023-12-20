<?php

namespace SRC\Sessions;

class Login
{
    private static function init()
    {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function login($obUser)
    {
        self::init();

        $_SESSION['usuario'] = [
            'id' => $obUser->id,
            'name' => $obUser->name,
            'email' => $obUser->email,
            'nivel' => $obUser->nivel,
            'agencia' => $obUser->agencia
        ];
        
        return true;
    }

    //
    public static function isLogged()
    {
        self::init();

        return isset($_SESSION['usuario']['id']);
    }

    public static function find(){
        $session = json_encode($_SESSION['usuario']);

        return json_decode($session);
    }

    public static function logout()
    {
        self::init();

        unset($_SESSION['usuario']);

        return true;
    }
}
