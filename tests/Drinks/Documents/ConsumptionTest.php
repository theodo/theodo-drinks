<?php

namespace Drinks\Document;

use Drinks\Document\Consumption;
use Drinks\Document\User;
use Drinks\Document\Drink;

/**
 * ConsumptionTest class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class ConsumptionTest extends \PHPUnit_Framework_TestCase
{
  public function testCreation()
  {
    $user = new User();
    $user->setName('benjamin');

    $drink = new Drink();
    $drink->setName('Coca-Cola');
    $drink->setPurchasePrice(385);
    $drink->setSalePrice(50);
    $drink->setQuantity(8);

    $consumption = new Consumption();
    $consumption->setDrink($drink);
    $consumption->setUser($user);

    $this->assertEquals('Coca-Cola', (string) $consumption->getDrink());
    $this->assertEquals('benjamin', (string) $consumption->getUser());
  }
}
