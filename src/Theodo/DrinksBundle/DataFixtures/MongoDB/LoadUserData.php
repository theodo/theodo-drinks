<?php

namespace Theodo\DrinksBundle\DataFixtures\ODM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Theodo\DrinksBundle\Document\User;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadUserData class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class LoadUserData implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $foo = new User();
        $foo->setUsername("foo");
        $foo->setEmail('foo@theodo.fr');

        $bar = new User();
        $bar->setUsername("bar");
        $bar->setEmail("bar@theodo.fr");

        $baz = new User();
        $baz->setUsername("baz");
        $baz->setEmail("baz@theodo.fr");

        $manager->persist($foo);
        $manager->persist($bar);
        $manager->persist($baz);
        $manager->flush();
    }

}
