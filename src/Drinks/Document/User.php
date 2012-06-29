<?php

namespace Drinks\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use Drinks\Document\Consumption;

/**
 * User class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 * @ODM\Document(repositoryClass="Drinks\Repository\UserRepository")
 */
class User
{
    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\String
     */
    private $name;

    /**
     * @ODM\Int
     */
    private $balance;

    /**
     * @ODM\ReferenceMany(targetDocument="Drinks\Document\Consumption", mappedBy="user")
     */
    private $consumptions = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->consumptions = new ArrayCollection();
    }

    /**
     * @param $consumptions
     */
    public function setConsumptions($consumptions)
    {
        $this->consumptions = $consumptions;
    }

    /**
     * @return array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getConsumptions()
    {
        return $this->consumptions;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return String
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * @return Integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Integer $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return Integer
     */
    public function getBalance()
    {
        return $this->balance;
    }
}
