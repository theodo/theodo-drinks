<?php

$loader = require_once __DIR__ . '/../vendor/autoload.php';

$app = new Drinks\Application();
$app->configure();
$app['loader'] = $loader;

$app->get('/', function() use ($app)
{
    $manager = $app['doctrine.odm.mongodb.dm'];

    $users = $manager->getRepository('Drinks\\Document\\User')
        ->findAll();

//    $drinks = $manager->getRepository('Drink\\Document\\Drink')
//        ->findAll();

    return $app['twig']->render('index.html.twig', array(
        'users'  => $users,
//        'drinks' => $drinks
    ));
});

return $app;
