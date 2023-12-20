<?php

namespace SRC\Http\Middleware;

use \SRC\Sessions\Android as SessionsAndroid;

class RequreLogoutAndroid{
    public function handle($request, $next){
        
        if(SessionsAndroid::isLogged()){
             $request->getRouter()->redirect('/');
        }
        
        return $next($request);
    }
}