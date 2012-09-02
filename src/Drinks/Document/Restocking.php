<?php

namespace Drinks\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Restocking class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 * @ODM\Document(repositoryClass="Drinks\Repository\RestockingRepository")
 */
class Restocking
{
    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\ReferenceMany(targetDocument="Drinks\Document\User", inversedBy="restockings")
     */
    private $users = array();

    /**
     * @ODM\EmbedMany(targetDocument="Drinks\Document\RestockingLine")
     */
    private $lines = array();

    /**
     * @ODM\Date
     */
    private $date;

    /**
     * @ODM\Int
     */
    private $amount;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->lines = new ArrayCollection();
    }

    /**
     * @ODM\PrePersist
     */
    public function prePersist()
    {
        if (null == $this->date) {
            $this->date = new \MongoDate();
        }

        foreach ($this->users as $user) {
            $user->resetDrinks();
        }
    }

    /**
     * @param integer $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array $lines
     */
    public function setLines($lines)
    {
        $this->lines = $lines;
    }

    /**
     * @return array
     */
    public function getLines()
    {
        return $this->lines;
    }

    /**
     * @param RestockingLine $line
     */
    public function addLine(RestockingLine $line)
    {
        $this->lines[] = $line;
    }
}
