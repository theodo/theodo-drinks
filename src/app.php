<?php

$loader = require_once __DIR__ . '/../vendor/autoload.php';

$app = new Drinks\Application();
$app->configure();
$app['loader'] = $loader;

$app->get('/', function () use ($app) {
    $manager = $app['doctrine.odm.mongodb.dm'];

    $users = $manager->getRepository('Drinks\\Document\\User')
        ->findAll();

    $drinks = $manager->getRepository('Drinks\\Document\\Drink')
        ->findAvailables();

    return $app['twig']->render('select.html.twig', array(
        'users'  => $users,
        'drinks' => $drinks,
    ));
})
->bind('homepage');

$app->post('/select', function () use ($app) {
    $values = $app['request']->request->get('selection');

    $drink = $app['doctrine.odm.mongodb.dm']->getRepository('Drinks\\Document\\Drink')
        ->findOneBy(array('name' => $values['drink']));

    if (false == $drink) {
        throw new \InvalidArgumentException("Drink with name \"{$values['drink']}\" not found.");
    }

    $user = $app['doctrine.odm.mongodb.dm']->getRepository('Drinks\\Document\\User')
        ->findOneBy(array('id' => $values['user_id']));

    if (false == $user) {
        throw new \InvalidArgumentException("User with name \"{$values['user']}\" not found.");
    }

    $transaction = new \Drinks\Document\Transaction();
    $transaction->setType(\Drinks\Document\Transaction::DEBIT);
    $transaction->setLabel((string) $drink);
    $transaction->setUser($user);

});

$app->get('/dashboard', function () use ($app) {
    $manager = $app['doctrine.odm.mongodb.dm'];

    $users = $manager->getRepository('Drinks\\Document\\User')
        ->findAll();

    $drinks = $manager->getRepository('Drinks\\Document\\Drink')
        ->findAll();

    return $app['twig']->render('dashboard.html.twig', array(
        'users'  => $users,
        'drinks' => $drinks
    ));
})
->bind('dashboard');

return $app;
