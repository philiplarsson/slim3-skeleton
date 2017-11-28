<?php

use App\Controllers\HomeController;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', HomeController::class . ':index')->setName('home');
