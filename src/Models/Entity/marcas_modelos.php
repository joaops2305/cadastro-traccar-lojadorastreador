<?php

namespace SRC\Models\Entity;

use \SRC\Database\Database;

class marcas_modelos
{
    public static function find($unid)
    {
        $dbSelect = (new Database('modelos'))->select("id=$unid")->fetchObject(self::class);

        if (!$dbSelect)
            return false;


        return $dbSelect;
    }

    //
    public static function findFabricante($unid)
    {
        $dbSelect = (new Database('fabricante'))->select("id=$unid")->fetchObject(self::class);

        if (!$dbSelect)
            return false;


        return $dbSelect;
    }

    public static function findModelo($unid)
    {
        $dbSelect = (new Database('modelos'))->select("id=$unid")->fetchObject(self::class);

        if (!$dbSelect)
            return false;


        return $dbSelect;
    }


    public static function opitonMarcas($unid = null)
    {
        $dbSelect = (new Database('fabricante'))->select();

        $fetchAll = jsonList($dbSelect->fetchAll());

        $option = "<option value=''>Selecione um Fabricante</option>";

        foreach ($fetchAll as $key => $obj) {
            if ($obj->id == $unid):
                $selected = "selected='selected'";
            else:
                $selected = null;
            endif;

            $jsonds = '{"unid":' . $obj->id . ', "Fabricante":"' . $obj->Fabricante . '"}';

            $option .= "<option $selected value=\"$obj->id\">" . utf8_encode($obj->Fabricante) . "</option>";
        }

        return $option;
    }

    public static function opitonModelos($unid = null, $select = null)
    {
        if($unid != null)
             $unid = "unid = $unid";

        $dbSelect = (new Database('modelos'))->select($unid);

        $fetchAll = jsonList($dbSelect->fetchAll());
        $rowCount = $dbSelect->rowCount();

        if ($rowCount > null):
            $option = "<option value=''>Selecione um Modelo</option>";

            foreach ($fetchAll as $key => $obj) {
                if ($obj->id == $select):
                    $selected = "selected='selected'";
                else:
                    $selected = null;
                endif;

                $option .= " <option $selected value=\"$obj->id\">" . utf8_encode($obj->modelo) . "</option> ";
            }

        else:

            $option = "<option value=''>Nem um Modelo Encontrado</option>";
        endif;

        return $option;
    }
}