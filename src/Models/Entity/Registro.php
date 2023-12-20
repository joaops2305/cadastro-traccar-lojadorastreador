<?php

namespace SRC\Models\Entity;

use SRC\Database\Database;

class Registro
{
    public static function events($unid,$event){
        return (new Database('history'))->select(" id=$unid AND category=$event ");
    }
}
