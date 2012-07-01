<?php

namespace Drinks\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

/**
 * RestockingControllerProvider class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class RestockingControllerProvider  implements ControllerProviderInterface
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

        $controllers->get('/stocks', function () use ($app) {
            $drinks = $app['doctrine.odm.mongodb.dm']->getRepository('Drinks\\Document\\Drink')
                ->findAll();

            return $app['twig']->render('Drink/stocks.html.twig', array(
                'drinks' => $drinks
            ));
        })
        ->bind('restocking_index');

        $controllers->match('/new', function (Request $request) use ($app) {
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
        ->bind('restocking_new')
        ->method('GET|POST');

        return $controllers;
    }
}
