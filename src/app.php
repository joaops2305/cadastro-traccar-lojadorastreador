<?php
date_default_timezone_set('America/Sao_Paulo');

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../Includes/globalFunction.php');

use \Includes\PHPMailer\Email;

use \SRC\Http\Router;
use \SRC\Controller\Views;
use \Includes\DotEnv\Environment;
use \SRC\Http\Middleware\Queue as MiddlewareQueue;

Environment::load(__DIR__.'/..');

define('URL', getenv("URL"));

Email::config(
    getenv("SMTP_EMPRESA"),
    getenv("SMTP_ADDRESS"),
    getenv("SMTP_USERNAME"),
    getenv("SMTP_PASSWORD"),
    getenv("SMTP_PORT"),
    getenv("SMTP_STARTTLS")
);

Views::init([
    "URL" => URL
]);

MiddlewareQueue::setMpa([
    'maintenance' => \SRC\Http\Middleware\Maintenance::class,
    'requred-login' => \SRC\Http\Middleware\RequreLogin::class,
    'requred-logout' => \SRC\Http\Middleware\RequreLogout::class
]);

MiddlewareQueue::setDefault([
    'maintenance'
]);