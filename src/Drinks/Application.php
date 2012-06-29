<?php

namespace Drinks;

use Silex\Application as BaseApplication;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Knp\Silex\ServiceProvider\DoctrineMongoDBServiceProvider;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

/**
 * Application class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class Application extends BaseApplication
{
    public function configure()
    {
        $this['root_dir']  = realpath(__DIR__.'/../..');
        $this['cache_dir'] = $this['root_dir'].'/cache';
        $this['log_dir']   = $this['root_dir'].'/log';

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
                'cache' => true,
            ),
            'twig.path' => __DIR__ . '/../views',
        ));

        $this['twig'] = $this->share($this->extend('twig', function($twig, $app) {
            $twig->addGlobal('layout', 'layout.html.twig');

            return $twig;
        }));
    }
}
