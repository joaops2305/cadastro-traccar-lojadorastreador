<?php

require './src/app.php';

use \SRC\Http\Router;

$obRouter = new Router(URL);

include __DIR__.'/routes/cardastro.php';

$obRouter->run()
        ->sendResponse();