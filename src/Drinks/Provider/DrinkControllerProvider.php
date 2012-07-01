<?php

namespace Drinks\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

/**
 * DrinkAccountProvider class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class DrinkControllerProvider  implements ControllerProviderInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->app = $app;

        $controllers = $app['controllers_factory'];

        /**
         * Show the balance and a form to repay it if needed.
         */
        $controllers->match('/select', function (Request $request) use ($app) {
            $manager = $app['doctrine.odm.mongodb.dm'];

            $user = $app['security']->getToken()->getUser();

            $drinks = $manager->getRepository('Drinks\\Document\\Drink')
                ->findAvailables();

            $drinkChoices = array();
            foreach ($drinks as $drink) {
                $drinkChoices[$drink->getId()] = (string) $drink;
            }

            $form = $app['form.factory']->create(new \Drinks\Form\Type\DrinkSelectionType(), array('user_id' => $user->getId()), array(
                'drink_choices' => $drinkChoices,
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

            return $app['twig']->render('Drink/select.html.twig', array(
                'form' => $form->createView(),
            ));
        })
        ->bind('drink_select')
        ->method('GET|POST');

        $controllers->get('/stocks', function () use ($app) {
            $drinks = $app['doctrine.odm.mongodb.dm']->getRepository('Drinks\\Document\\Drink')
                ->findAll();

            return $app['twig']->render('Drink/stocks.html.twig', array(
                'drinks' => $drinks
            ));
        })
        ->bind('drink_stocks');

        $controllers->match('/restocking', function (Request $request) use ($app) {
            $dm = $app['doctrine.odm.mongodb.dm'];

            $users = $dm->getRepository('Drinks\\Document\\User')->findAll();
            $drinks = $dm->getRepository('Drinks\\Document\\Drink')->findAll();

            $form = $app['form.factory']->create(new \Drinks\Form\Type\RestockingType(), null, array(
                'drinks' => $drinks,
                'users'  => $users
            ));

            if ($request->isMethod('post')) {
                $form->bindRequest($request);

                if ($form->isValid()) {
                    $restocking = $app['restocking.factory']->createRestocking($form->getData());

                    $dm->persist($restocking);
                    $dm->flush();

                    return $app->redirect($app['url_generator']->generate('drink_stocks'));
                }
            }

            return $app['twig']->render('Drink/restocking.html.twig', array(
                'form' => $form->createView()
            ));
        })
        ->bind('drink_restocking')
        ->method('GET|POST');

        return $controllers;
    }
}
