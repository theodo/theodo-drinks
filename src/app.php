<?php

$loader = require_once __DIR__ . '/../vendor/autoload.php';

$app = new Drinks\Application();
$app->configure();

$app->get('/', function () use ($app) {
    return $app->redirect($app['url_generator']->generate('drink_select'));
})
->bind('homepage');

return $app;
