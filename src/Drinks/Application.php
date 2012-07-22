<?php

namespace Drinks;

use Silex\Application as BaseApplication;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Knp\Silex\ServiceProvider\DoctrineMongoDBServiceProvider;
use Drinks\Factory\TransactionFactory;
use Drinks\Factory\RestockingFactory;
use Drinks\Security\Provider\UserProvider;

use Symfony\Component\Security\Http\Firewall\RememberMeListener;

// Controller providers usage.
use Drinks\Provider\UserControllerProvider;
use Drinks\Provider\DrinkControllerProvider;
use Drinks\Provider\RestockingControllerProvider;

/**
 * Application class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class Application extends BaseApplication
{
    public function configure()
    {
        $this['root_dir']   = realpath(__DIR__.'/../..');
        $this['src_dir']    = realpath(__DIR__);
        $this['vendor_dir'] = $this['root_dir'].'/vendor';
        $this['cache_dir']  = $this['root_dir'].'/cache';
        $this['log_dir']    = $this['root_dir'].'/log';
        $this['view_dir']   = $this['src_dir'].'/../views';

        AnnotationDriver::registerAnnotationClasses();

        $this['debug'] = $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1';

        $this->register(new DoctrineMongoDBServiceProvider(), array(
            'doctrine.odm.mongodb.connection_options' => array(
                'database' => 'theodo-drinks',
                'host' => 'mongodb://localhost',
            ),
            'doctrine.odm.mongodb.documents' => array(
                array(
                    'type' => 'annotation',
                    'path' => array(__DIR__ . '/Document'),
                    'namespace' => 'Drinks\\Document'
                )
            ),
            'doctrine.odm.mongodb.proxies_dir'   => $this['cache_dir'],
            'doctrine.odm.mongodb.hydrators_dir' => $this['cache_dir'],
        ));

        $this->register(new MonologServiceProvider(), array(
            'monolog.logfile' => $this['log_dir'].'/development.log',
        ));

        $this->register(new TwigServiceProvider(), array(
            'twig.options' => array(
                'debug' => $this['debug'],
                'cache' => $this['cache_dir'],
            ),
            'twig.path' => array($this['view_dir']),
        ));

        $this['twig'] = $this->share($this->extend('twig', function($twig, $app) {
            $twig->addGlobal('layout', 'layout.html.twig');

            return $twig;
        }));

        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new SessionServiceProvider());
        $this->register(new FormServiceProvider());
        $this->register(new ValidatorServiceProvider());
        $this->register(new TranslationServiceProvider(), array('locale_fallback' => 'fr'));

        $app = $this;
        $this['transaction.factory'] = $this->share(function () use ($app) {
            return new TransactionFactory($app['translator']);
        });
        $this['restocking.factory'] = $this->share(function () use ($app) {
            return new RestockingFactory($app['doctrine.odm.mongodb.dm']);
        });

        $this->configureSecurity();

        $this->mountControllerProviders();
    }

    /**
     * Configure the security.
     */
    public function configureSecurity()
    {
        $app = $this;

        $this->register(new SecurityServiceProvider());

        $this['security.authentication_listener.remember_me._proto'] = $app->protect(function ($name, $options) use ($app) {
            return $app->share(function () use ($app, $options, $name) {
                if (isset($options['token_provider'])) {
                    $servicesClass = 'Symfony\\Component\\Security\\Http\\RememberMe\\PersistentTokenBasedRememberMeServices;';
                    unset($options['token_provider']);
                } else {
                    $servicesClass = 'Symfony\\Component\\Security\\Http\\RememberMe\\TokenBasedRememberMeServices';
                }

                $app['security.authentication_listener.remember_me.services'] = $app->share(function () use ($app, $servicesClass, $name, $options) {
                    $key = $options['key'];
                    unset($options['key']);

                    return new $servicesClass(
                        array($app['security.user_provider.'.$name]),
                        $key,
                        $name,
                        $options,
                        $app['logger']
                    );
                });


                return new RememberMeListener(
                    $app['security'],
                    $app['security.authentication_listener.remember_me.services'],
                    $app['security.authentication_manager'],
                    $app['logger'],
                    $app['dispatcher']
                );
            });
        });

        $this['security.firewalls'] = array(
            'login' => array(
                'pattern' => '^/user/login$',
            ),
            'front' => array(
                'pattern' => '^/',
                'form' => array('login_path' => '/user/login', 'check_path' => '/login_check'),
                'users'=> $this->share(function () use ($app) {
                    return new UserProvider($app['doctrine.odm.mongodb.dm'], 'Drinks\\Document\\User');
                }),
                'remember_me' => array('key' => isset($_SERVER['SECRET']) ? $_SERVER['SECRET'] : 'notsecrettoken')
            ),
        );
    }

    /**
     * Mount controller providers of Theodo Drinks application.
     */
    public function mountControllerProviders()
    {
        $this->mount('/user', new UserControllerProvider());
        $this->mount('/drink', new DrinkControllerProvider());
        $this->mount('/restocking', new RestockingControllerProvider());
    }
}
