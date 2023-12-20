<?php

namespace SRC\Sessions;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Android
{
    public static function login($obUser)
    {
        $payload = [
            'id' => $obUser->id,
            'name' => $obUser->name,
            'email' => $obUser->email
        ];

        $jwt = JWT::encode($payload, getenv("SECRET_KEY"), 'HS256');

        setcookie('Token', $jwt, (time() + (6 * 3600)), '/');

        return true;
    }

    //
    public static function isLogged()
    {
        if (!isset($_COOKIE['Token']))
            return false;

        $session = JWT::decode($_COOKIE['Token'], new Key(getenv("SECRET_KEY"), 'HS256'));

        return isset($session->id);
    }

    //
    public static function logado(){
        return JWT::decode($_COOKIE['Token'], new Key(getenv("SECRET_KEY"), 'HS256'));
    }

    public static function logout()
    {
        setcookie('Token');
        return true;
    }
}