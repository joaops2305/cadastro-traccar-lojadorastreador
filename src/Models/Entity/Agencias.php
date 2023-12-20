<?php

namespace SRC\Models\Entity;

use \SRC\Database\Database;

class Agencias
{
    public static function find($unid)
    {
        $select = (new Database('agencias'))->select("id=$unid")->fetchObject(self::class);

        if (!$select)
            return false;

        $temp = json_decode($select->jsondb);
        $temp->id = $select->id;

        return $temp;
    }

    //
    public static function options($agencia = null)
    {
        // session_start();
        $connected = (isset($_SESSION['usuario']['agencia']) ? $_SESSION['usuario']['agencia'] : null);

        $dbSelect = (new Database('agencias'))->select(null, "JSON_EXTRACT(jsondb,'$.agencia') ASC");

        $fetchAll = $dbSelect->fetchAll();
        $rowCount = $dbSelect->rowCount();

        $option = "<select class=\"form-control\" name=\"agencia\" id=\"agencia\">";
        $option .= "<option value=\"\">Selecione Uma Agência</option required>";

        $selected = null;

        foreach ($fetchAll as $key => $obj) {
            $find = self::find($obj['id']);
            //
            if ($agencia == $find->id):
                $selected = "selected='selected'";
            else:
                $selected = "";
            endif;

            $option .= "<option $selected value='" . $find->id . "'>" . $find->agencia . "</option required>";
        }

        $option .= "</select>";

        return $option;
    }
}