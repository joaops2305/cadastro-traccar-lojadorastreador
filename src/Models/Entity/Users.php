<?php

namespace SRC\Models\Entity;

use SRC\Database\Database;

class Users{
    private $id;
    private $name;
    private $email;

    public static function getUserByEmail($data){         
         $select = (new Database('usuarios'))->select("JSON_EXTRACT(jsondb,'$.email') = '$data' OR JSON_EXTRACT(jsondb,'$.username') = '$data'")->fetchObject(self::class);

         if(!$select)
             return false;

         $temp = json_decode($select->jsondb);            
         $temp->id = $select->id;
         
         return $temp;
    }

    public static function find($unid){
         $select = (new Database('usuarios'))->select("id=$unid")->fetchObject(self::class);

         if(!$select)
             return false;

         $temp = json_decode($select->jsondb);    
         $temp->agencia = empty($temp->agencia) ? null : $temp->agencia;
         $temp->id = $select->id;
         
         return $temp;
    }

    //
    public static function checkout($temp){         
        $sql = " JSON_EXTRACT(jsondb,'$.email') = '$temp->email' OR JSON_EXTRACT(jsondb,'$.username') = '$temp->username' ";

        if(isset($temp->id))
            $sql = " id != $temp->id AND JSON_EXTRACT(jsondb,'$.email') = '$temp->email' OR id != $temp->id AND JSON_EXTRACT(jsondb,'$.username') = '$temp->username' ";

        $dbSelect = (new Database('usuarios'))->select($sql);

        $rowCount = $dbSelect->rowCount();

        return $rowCount;
    }
}