<?php

namespace Drinks;

use Silex\Application as BaseApplication;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Knp\Silex\ServiceProvider\DoctrineMongoDBServiceProvider;
use Drinks\Factory\TransactionFactory;
use Drinks\Factory\RestockingFactory;
use Drinks\Security\Provider\UserProvider;

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
