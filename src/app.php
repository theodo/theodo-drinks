<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Provider\TwigServiceProvider;

$app = new Silex\Application();

$app['debug'] = $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1';


$app->register(new TwigServiceProvider(), array(
     'twig.path' => __DIR__ . '/views',
));


$app->get('/', function() use ($app) {
  return $app['twig']->render('index.html.twig');
});

return $app;