<?php

namespace Includes\Traccar;

use \stdClass;

class ApiTraccar {
    public static $host = 'ok';
    public static $token = 'ok';
    public static $cookie;
    private static $jsonA = 'Accept: application/json';
    private static $jsonC = 'Content-Type: application/json';
    private static $urlEncoded = 'Content-Type: application/x-www-form-urlencoded';

    //Coneccart
    public static function Conectar() {
        $token = getenv("TOKEN_TRACCAR");

        if(isset($_COOKIE['GSTOR'])):
            return self::$cookie = "JSESSIONID=".$_COOKIE['GSTOR'];
        else:
            self::curl('/api/session?token='.$token, 'GET', '', null, array(self::$urlEncoded));

            $result = preg_replace('/JSESSIONID=/', '', self::$cookie, 1);

            setcookie("GSTOR", $result, time() + (86400 * 30), "/");

            return self::$cookie;
        endif;
    }

    //
    public static function cardUser($data) {
        self::Conectar();

        return self::curl('/api/users', 'POST', self::$cookie, $data, array(self::$jsonC))->response;
    }

    //
    public static function findUser($temp) {
        self::Conectar();

        $users = json_decode(self::curl('/api/users', 'GET', self::$cookie, null, array(self::$jsonC))->response, true);

        $find = null;

        for($i = 0; $i < count($users); $i++) {
            if(isset($users[$i]['attributes']['informacoes']['cpf'])) {
                if($users[$i]['email'] == $temp->email && $users[$i]['attributes']['informacoes']['cpf'] == $temp->cpf) {
                    $find = json_encode($users[$i]);
                }
            }
        }

        return $find;
    }

    public static function checkUser($data) {
        self::Conectar();

        $temp = json_decode($data);

        $users = json_decode(self::curl('/api/users', 'GET', self::$cookie, null, array(self::$jsonC))->response, true);

        $cont = 0;

        for($i = 0; $i < count($users); $i++) {

            if($users[$i]['login'] == $temp->login) {
                $cont++;
            }

            if($users[$i]['email'] == $temp->email) {
                $cont++;
            }
        }

        return $cont;
    }

    //Devices
    public static function Devices($request) {
        self::Conectar();

        // print '<pre>'; print_r(self::$cookie); print'</pre>'; exit();        

        return self::curl('/api/devices', 'GET', self::$cookie, '', array());
    }

    //
    public static function findDevice($uniqueId) {
        self::Conectar();

        $tempCurl = self::curl('/api/devices?uniqueId='.$uniqueId, 'GET', self::$cookie, null, array(self::$jsonC));

        return $tempCurl->response;
    }

    //
    public static function checkDevice($uniqueId) {
        self::Conectar();

        $checkDevice = self::curl('/api/devices/?uniqueId='.$uniqueId, 'GET', self::$cookie, null, array(self::$jsonC));

        return $checkDevice->response;
    }

    //
    public static function cardDevice($temp) {
        self::Conectar();

        $data = '{
            "id": -1,
            "name": "Dispositivo",
            "uniqueId": "'.$temp->uniqueId.'",
            "phone": "'.$temp->phone.'",
            "model": "'.$temp->model.'",
            "contact": "",
            "category": "arrow",
            "disabled": false,
            "attributes":{
                "marca":"'.$temp->marca.'"
            }
           }';

        return self::curl('/api/devices', 'POST', self::$cookie, $data, array(self::$jsonC));
    }

    //
    public static function editDevice($imei) {
        self::Conectar();

        $findDevice = json_decode(self::findDevice($imei), true)[0];
        $findDevice['attributes']['registro'] = true;

        $data = json_encode($findDevice);

        return self::curl('/api/devices/'.$findDevice['id'], 'PUT', self::$cookie, $data, array(self::$jsonC))->response;
    }
    //
    public static function permissions($userId, $deviceId) {
        self::Conectar();

        $data = json_encode([
            'userId' => $userId,
            'deviceId' => $deviceId
        ]);

        return self::curl('/api/permissions', 'POST', self::$cookie, $data, array(self::$jsonC));
    }

    //curl	
    public static function curl($task, $method, $cookie, $data, $header) {
        $res = new stdClass();
        $res->responseCode = '';
        $res->error = '';
        $header[] = "Cookie: ".$cookie;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, getenv("HOST_TRACCAR").$task);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if($method == 'POST' || $method == 'PUT' || $method == 'DELETE') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $data = curl_exec($ch);
        $size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

        if(preg_match('/^Set-Cookie:\s*([^;]*)/mi', substr($data, 0, $size), $c) == 1)
            self::$cookie = $c[1];
        $res->response = substr($data, $size);

        if(!curl_errno($ch)) {
            $res->responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        } else {
            $res->responseCode = 400;
            $res->error = curl_error($ch);
        }

        curl_close($ch);
        return $res;
    }
}
