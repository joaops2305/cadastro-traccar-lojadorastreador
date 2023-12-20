<?php

namespace SRC\Controller;

class Views
{
    /**
     * VARIÁVEIS PADROES DA VIEW
     */
    private static $vars = [];

    public static function init($vars = [])
    {
        self::$vars = $vars;
    }

    private static function getConetenView($view)
    {
        $file = './resources/views/' . $view . '.html';
        return file_exists($file) ? file_get_contents($file) : 'Pagina Não Encontrada';
    }

    //
    public static function render($view, $vars = [])
    {
        //CONTEUDO DA VIEW 
        $contenView = self::getConetenView($view);

        //MERGE DE VARIÁVEIS DO LAYIOUT
        $vars = array_merge(self::$vars, $vars);

        //CHAVES DO ARRAY DE VARIAVEIS
        $keys = array_keys($vars);
        $keys = array_map(function ($item) {
            return '{{' . $item . '}}'; /*  */
        }, $keys);

        return @str_replace($keys, array_values($vars), $contenView);
    }

    public static function renderForm($form, $vars = []){
        //CONTEUDO DA VIEW 
        $contenView = self::render($form, $vars);

        return preg_replace('/{{(.*?)}}/s', '', $contenView);
    }
}