<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Provider\TwigServiceProvider;
use Knp\Silex\ServiceProvider\DoctrineMongoDBServiceProvider;

$app = new Silex\Application();

$app['debug'] = $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1';


$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));

$app->register(new DoctrineMongoDBServiceProvider(), array(
    'doctrine.odm.mongodb.connection_options' => array(
        'database' => 'theodo-drinks',
        'host' => 'localhost',
    ),
    'doctrine.odm.mongodb.documents' => array(

    )
));

$app->get('/', function() use ($app)
{
    return $app['twig']->render('index.html.twig');
});

return $app;