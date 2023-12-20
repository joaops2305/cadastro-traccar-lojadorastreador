<?php

namespace Includes\DotEnv;

class Environment{
     public static function load($dir){                
         //VERIFICA SE O ARQUIVO .ENV EXISTE
         if(!file_exists($dir.'/.env')){            
             return false;
         }
    
         //DEFINE AS VARIÁVEIS DE AMBIENTE
         $lines = file($dir.'/.env');    

         foreach($lines as $line){
             putenv(trim($line));
          }
      }
}