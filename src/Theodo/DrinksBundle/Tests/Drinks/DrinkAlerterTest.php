<?php

namespace Drinks;

/**
 * DrinkAlerterTest class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class DrinkAlerterTest extends \PHPUnit_Framework_TestCase
{
    protected $alerter;

    protected function setUp()
    {
        parent::setUp();

        $mailer = $this->getMockBuilder('\\Swift_Mailer')
            ->disableOriginalConstructor()
            ->getMock();

        $twig = $this->getMockBuilder('\\Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock();

        $logger = $this->getMockBuilder('\\Monolog\\Logger')
            ->disableOriginalConstructor()
            ->getMock();

        $this->alerter = new \Drinks\DrinkAlerter($mailer, $twig, $logger);
    }

    public function testAlertIsExhausted()
    {
        $drink = new \Theodo\DrinksBundle\Document\Drink();
        $drink->setName('Coca-Cola');

        $notified = $this->alerter->alertIsExhausted($drink);
        $this->assertTrue($notified);
    }
}
