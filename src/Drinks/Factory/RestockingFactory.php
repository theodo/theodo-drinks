<?php

namespace Drinks\Factory;

use Drinks\Document\Restocking;
use Drinks\Document\RestockingLine;
use Drinks\Document\Drink;
use Drinks\Document\User;

/**
 * RestockingFactory class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class RestockingFactory
{
    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    public function __construct(\Doctrine\ODM\MongoDB\DocumentManager $manager)
    {
        $this->dm = $manager;
    }

    /**
     * @param  array                       $datas
     * @return \Drinks\Document\Restocking
     */
    public function createRestocking(array $datas)
    {
        $ids = array_map(function ($value) {
            return new \MongoId($value);
        }, $datas['user_ids']);

        $users = $this->dm->getRepository('Drinks\\Document\\User')->findByIds($ids);

        $collection = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($users as $user) {
            $collection->add($user);
        }

        $restocking = new Restocking();
        $restocking->setUsers($collection);
        $restocking->setAmount($datas['amount'] * 100);

        foreach ($datas['lines'] as $line) {
            $restocking->addLine($this->createRestockingLine($line));
        }

        return $restocking;
    }

    /**
     * @param  array                           $datas
     * @return \Drinks\Document\RestockingLine
     */
    public function createRestockingLine(array $datas)
    {
        $drink = $this->dm->getRepository('Drinks\\Document\\Drink')->findOneBy(array('_id' => new \MongoId($datas['drink_id'])));

        if (!$drink) {
            throw new \InvalidArgumentException("There is no drink with id {$datas['drink_id']}.");
        }

        $line = new RestockingLine();
        $line->setDrink($drink);
        $line->setQuantity($datas['quantity']);

        return $line;
    }
}
