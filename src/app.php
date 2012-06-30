<?php

$loader = require_once __DIR__ . '/../vendor/autoload.php';

$app = new Drinks\Application();
$app->configure();
$app['loader'] = $loader;

$app->mount('/user', new Drinks\Provider\UserAccountProvider());

use Symfony\Component\HttpFoundation\Request;

$app->get('/', function () use ($app) {
    return $app->redirect($app['url_generator']->generate('drink_select'));
})
->bind('homepage');

$app->match('/have-a-drink', function (Request $request) use ($app) {
    $manager = $app['doctrine.odm.mongodb.dm'];

    $users = $manager->getRepository('Drinks\\Document\\User')
        ->findAll();

    $drinks = $manager->getRepository('Drinks\\Document\\Drink')
        ->findAvailables();

    $userChoices = array();
    foreach ($users as $user) {
        $userChoices[$user->getId()] = (string) $user;
    };

    $drinkChoices = array();
    foreach ($drinks as $drink) {
        $drinkChoices[$drink->getId()] = (string) $drink;
    }

    $form = $app['form.factory']->create(new \Drinks\Form\Type\DrinkSelectionType(), array(
        'userChoices'  => $userChoices,
        'drinkChoices' => $drinkChoices
    ));

    if ($request->isMethod('post')) {
        $form->bindRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $drink = $app['doctrine.odm.mongodb.dm']->getRepository('Drinks\\Document\\Drink')
                ->findOneBy(array('id' => $data['drink_id']));

            $user = $app['doctrine.odm.mongodb.dm']->getRepository('Drinks\\Document\\User')
                ->findOneBy(array('id' => $data['user_id']));

            if ('now' == $data['payment']) {
                list($credit, $debit) = $app['transaction.factory']->createCompleteTransaction($user, $drink);

                $manager->persist($credit);
                $manager->persist($debit);
            } else {
                $debit = $app['transaction.factory']->createDebit($user, $drink);

                $manager->persist($debit);
            }

            $manager->flush();

            return $app->redirect($app['url_generator']->generate('user_transactions'));
        }
    }

    return $app['twig']->render('select.html.twig', array(
        'form' => $form->createView(),
    ));
})
->bind('drink_select')
->method('GET|POST');

return $app;
