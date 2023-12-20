<?php

namespace SRC\Http\Middleware;

use \SRC\Sessions\Login as SessionLogin;

class RequreLogout{
    public function handle($request, $next){
        
        if(SessionLogin::isLogged()){
             $request->getRouter()->redirect('/');
        }
        
        return $next($request);
    }
}