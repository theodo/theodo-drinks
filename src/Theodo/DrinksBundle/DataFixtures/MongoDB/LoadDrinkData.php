<?php

namespace Theodo\DrinksBundle\DataFixtures\ODM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Theodo\DrinksBundle\Document\Drink;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadDrinkData class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class LoadDrinkData implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $coca = new Drink();
        $coca->setName("Coca Cola");
        $coca->setPurchasePrice(385);
        $coca->setSalePrice(50);
        $coca->setQuantity(8);

        $cocaZero = new Drink();
        $cocaZero->setName("Coca Cola Zero");
        $cocaZero->setPurchasePrice(385);
        $cocaZero->setSalePrice(50);
        $cocaZero->setQuantity(6);

        $liptonic = new Drink();
        $liptonic->setName("Liptonic");
        $liptonic->setPurchasePrice(385);
        $liptonic->setSalePrice(50);
        $liptonic->setQuantity(6);

        $manager->persist($coca);
        $manager->persist($cocaZero);
        $manager->persist($liptonic);
        $manager->flush();
    }

}
