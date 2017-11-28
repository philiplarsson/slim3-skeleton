<?php

use Dotenv\Dotenv;
use Noodlehaus\Config;

require '../vendor/autoload.php';

// Load dotenv
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = new Dotenv(__DIR__ .'/../');
    $dotenv->load();
}

if (getenv('DEBUG') === 'true') {
    // Turn this on in development mode to
    // get information about errors (without it, Slim will at least log errors so
    // if you’re using the built in PHP webserver then you’ll see them in the
    // console output which is helpful).
    $config['displayErrorDetails'] = true;
}

// The second line allows the web server to
// set the Content-Length header which makes Slim behave more predictably.
$config['addContentLengthHeader'] = false;

$app = new \Slim\App([ 'settings' => $config ]);

$container = $app->getContainer();

$container['config'] = function ($c) {
    return new Config(__DIR__ . '/../config/app.php');
};

$container['view'] = function ($container) {
    if ($container->config->get('debug') === "true") {
        $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
            'cache' => false,
            'debug' => true
        ]);
    } else {
        $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views', [
            'cache' => __DIR__ . '/../cache/twig',
            'debug' => false
        ]);
    }

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));
    $view->addExtension(new \Twig_Extension_Debug());

    // Add app name as global
    $view->getEnvironment()->addGlobal('appName', $container->config->get('app-name'));

    return $view;
};

require_once __DIR__ . '/../routes/web.php';
