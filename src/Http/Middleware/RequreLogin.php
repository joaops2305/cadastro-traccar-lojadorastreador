<?php

namespace SRC\Http\Middleware;

use \SRC\Sessions\Login as SessionLogin;

class RequreLogin{
    public function handle($request, $next){
        
        if(!SessionLogin::isLogged()){
             $request->getRouter()->redirect('/login');
        }
        
        return $next($request);
    }
}