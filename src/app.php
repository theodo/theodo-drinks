<?php

$loader = require_once __DIR__ . '/../vendor/autoload.php';

$app = new Drinks\Application();
$app->configure();
$app['loader'] = $loader;

use Symfony\Component\HttpFoundation\Request;

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

            return $app->redirect($app['url_generator']->generate('dashboard', array('name' => $user->getName())));
        }
    }

    return $app['twig']->render('select.html.twig', array(
        'form' => $form->createView(),
    ));
})
->bind('drink_select')
->method('GET|POST');

$app->get('/dashboard/{name}', function ($name) use ($app) {
    $manager = $app['doctrine.odm.mongodb.dm'];

    $user = $manager->getRepository('Drinks\\Document\\User')
        ->findOneBy(array('name' => $name));

    $transactions = $manager->getRepository('Drinks\\Document\\Transaction')
        ->findByUser($user);

    return $app['twig']->render('dashboard.html.twig', array(
        'user'  => $user,
        'transactions' => $transactions
    ));
})
->bind('dashboard');

$app->get('/pay/{id}', function ($id) use ($app) {
    $manager = $app['doctrine.odm.mongodb.dm'];

    $transaction = $manager->getRepository('Drinks\\Document\\Transaction')
        ->findBy(array('id' => $id));

    $user  = $transaction->getUser();
    $drink = $transaction->getDrink();

    $credit = $app['transaction.factory']->createCredit($user, $drink);
    $manager->persist($credit);
    $manager->flush();

    return $app->redirect($app['url_generator']->generate('dashboard', array('name' => $user->getName())));
})
->convert('id', function ($id) { return (int) $id; })
->bind('drink_pay');

return $app;
