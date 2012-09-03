<?php

namespace Drinks;

use Silex\Application as BaseApplication;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Knp\Silex\ServiceProvider\DoctrineMongoDBServiceProvider;
use Drinks\Factory\TransactionFactory;
use Drinks\Factory\RestockingFactory;
use Drinks\Security\Provider\UserProvider;
use Drinks\Provider\OAuthSecurityServiceProvider;

// Controller providers usage.
use Drinks\Provider\UserControllerProvider;
use Drinks\Provider\DrinkControllerProvider;
use Drinks\Provider\RestockingControllerProvider;

use Knp\Bundle\OAuthBundle\Security\Core\Authentication\Provider\OAuthProvider;

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
        $this['config_dir'] = $this['src_dir'].'/../config';
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

        $app['buzz.client.factory'] = $app->protect(function ($client) use ($app) {
            return $app->share(function () use ($client, $app) {
                $clients = array(
                    'curl'      => '\Buzz\Client\Curl',
                    'multicurl' => '\Buzz\Client\MultiCurl',
                    'stream'    => '\Buzz\Client\FileGetContent',
                );

                if (false == isset($clients[$client])) {
                    throw new \InvalidArgumentException(sprintf('The client "%s" does not exist, curl, multicurl and stream availables.', $client));
                }

                $client = $clients[$client];

                return new $client();
            });
        });

        $this['buzz.client'] = $app['buzz.client.factory']('curl');

        $this->register(new OAuthSecurityServiceProvider());

        $loader = \Symfony\Component\Yaml\Yaml::parse($this['config_dir'].'/config.yml');

        $this['security.firewalls'] = array(
            'front' => array(
                'pattern' => '^.*',
                'oauth' => array(
                    'oauth_provider'    => 'google',
                    'infos_url'         => 'https://www.googleapis.com/oauth2/v1/userinfo',
                    'username_path'     => 'email',
                    'scope'             => 'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email',
                    'login_path'        => '/login',
                    'check_path'        => '/login_check',
                    'failure_path'      => '/',
                    'client_id'         => $loader['google']['client_id'],
                    'secret'            => $loader['google']['secret'],
                ),
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
