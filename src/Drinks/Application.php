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
        $app = $this;

        $app['debug'] = $_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1';

        $app->register(new TwigServiceProvider(), array(
            'twig.path' => __DIR__ . '/../views',
        ));

        $app->register(new MonologServiceProvider(), array(
            'monolog.logfile' => __DIR__ . '/../../log/development.log',
        ));

        $app->register(new DoctrineMongoDBServiceProvider(), array(
            'doctrine.odm.mongodb.connection_options' => array(
                'database' => 'theodo-drinks',
                'host' => 'localhost',
            ),
            'doctrine.odm.mongodb.documents' => array(
                array(
                    'type' => 'annotation',
                    'path' => array(__DIR__ . '/Document'),
                    'namespace' => 'Drinks\\Document'
                )
            )
        ));

        AnnotationDriver::registerAnnotationClasses();
    }
}
