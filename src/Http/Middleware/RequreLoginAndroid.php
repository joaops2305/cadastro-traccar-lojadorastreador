<?php

namespace SRC\Http\Middleware;

use \SRC\Sessions\Android as SessionsAndroid;

class RequreLoginAndroid{
    public function handle($request, $next){
        
        if(!SessionsAndroid::isLogged()){
             $request->getRouter()->redirect('/login');
        }
        
        return $next($request);
    }
}