<?php

namespace Drinks\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Drinks\Document\Drink;
use Drinks\Document\User;

/**
 * Consumption class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 * @ODM\Document(repositoryClass="Drinks\Repository\ConsumptionRepository")
 */
class Consumption
{
    /** @ODM\Id */
    private $id;

    /**
     * @ODM\ReferenceOne(targetDocument="Drinks\Document\User", inversedBy="consumptions")
     */
    private $user;

    /**
     * @ODM\ReferenceOne(targetDocument="Drinks\Document\Drink", inversedBy="consumptions")
     */
    private $drink;

    /**
     * @param Drinks\Document\Drink $drink
     */
    public function setDrink(\Drinks\Document\Drink $drink)
    {
        $this->drink = $drink;
    }

    /**
     * @return Drinks\Document\Drink
     */
    public function getDrink()
    {
        return $this->drink;
    }

    /**
     * @param Drinks\Document\User $user
     */
    public function setUser(\Drinks\Document\User $user)
    {
        $this->user = $user;
    }

    /**
     * @return Drinks\Document\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Integer
     */
    public function getId()
    {
        return $this->id;
    }
}
