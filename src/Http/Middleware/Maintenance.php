<?php

namespace SRC\Http\Middleware;

class Maintenance{
    public function handle($request, $next){
        if(getenv('MAINTENANCE') == 'true'){
             throw new \Exception("Pagina em Manuteção", 200);
        }
        
        return $next($request);               
    }
}