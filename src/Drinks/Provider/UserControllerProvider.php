<?php

namespace Drinks\Provider;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

/**
 * UserAccountProvider class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class UserControllerProvider  implements ControllerProviderInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param  Application                 $app
     * @return \Silex\ControllerCollection
     */
    public function connect(Application $app)
    {
        $this->app = $app;

        $controllers = $app['controllers_factory'];

        /**
         * Show the balance and a form to repay it if needed.
         */
        $controllers->get('/balance', function () use ($app) {
            $form = $app['form.factory']->create(new \Drinks\Form\Type\TransactionRepaymentType());

            return $app['twig']->render('User/balance.html.twig', array(
                'user' => $app['user'],
                'form' => $form->createView(),
            ));
        })
        ->bind('user_balance')
        ->before(array($this, 'findUser'));

        /**
         * List the users' transactions.
         */
        $controllers->get('/transactions', function () use ($app) {
            $transactions = $app['doctrine.odm.mongodb.dm']->getRepository('Drinks\\Document\\Transaction')
                ->findByUser($app['user']);

            return $app['twig']->render('User/transactions.html.twig', array(
                'user'         => $app['user'],
                'transactions' => $transactions,
            ));
        })
        ->bind('user_transactions')
        ->before(array($this, 'findUser'));

        /**
         * Repay the debt by the amount the user wants.
         */
        $controllers->post('/repay', function (Request $request) use ($app) {
            $form = $app['form.factory']->create(new \Drinks\Form\Type\TransactionRepaymentType());

            $form->bindRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();

                $manager = $app['doctrine.odm.mongodb.dm'];
                $user    = $app['user'];

                $credit = $app['transaction.factory']->createRepayment($user, $data['amount'] * 100);
                $manager->persist($credit);
                $manager->flush();

                return $app->redirect($app['url_generator']->generate('user_transactions'));
            }

            return $app['twig']->render('User/balance.html.twig', array(
                'user' => $app['user'],
                'form' => $form->createView(),
            ));
        })
        ->bind('user_repay')
        ->before(array($this, 'findUser'));

        /**
         * Repay the users' debt.
         */
        $controllers->get('/repay-all', function () use ($app) {
            $manager = $app['doctrine.odm.mongodb.dm'];

            $credit = $app['transaction.factory']->createRepayment($app['user'], abs($app['user']->getBalance()));
            $manager->persist($credit);
            $manager->flush();

            return $app->redirect($app['url_generator']->generate('user_transactions'));
        })
        ->bind('user_repay_all')
        ->before(array($this, 'findUser'));

        $controllers->match('/password', function (Request $request) use ($app) {
            $user    = $app['user'];

            $form = $app['form.factory']->create(new \Drinks\Form\Type\UserPasswordType(), array('id' => $user->getId()));

            if ($request->isMethod('post')) {
                $form->bindRequest($request);
                if ($form->isValid()) {
                    $data = $form->getData();

                    $manager = $app['doctrine.odm.mongodb.dm'];

                    $pass = $data['password'];
                    $encoder = $app['security.encoder_factory']->getEncoder($user);
                    $password = $encoder->encodePassword($pass, $user->getSalt());
                    $user->setPassword($password);

                    $manager->persist($user);
                    $manager->flush();

                    return $app->redirect($app['url_generator']->generate('user_password'));
                }
            }

            return $app['twig']->render('User/password.html.twig', array(
                'user' => $user,
                'form' => $form->createView()
            ));
        })
        ->bind('user_password')
        ->method('GET|POST')
        ->before(array($this, 'findUser'));

        $controllers->get('/login', function(Request $request) use ($app) {
            return $app['twig']->render('User/login.html.twig', array(
                'error'         => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
            ));
        })
        ->bind('user_login');

        return $controllers;
    }

    /**
     * @todo use session.
     */
    public function findUser()
    {
        $user = $this->app['doctrine.odm.mongodb.dm']->getRepository('Drinks\\Document\\User')
            ->findOneBy(array('name' => (string) $this->app['security']->getToken()->getUser()));

        $this->app['user'] = $user;
    }
}
