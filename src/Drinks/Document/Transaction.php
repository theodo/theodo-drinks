<?php

namespace Drinks\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Drinks\Document\Drink;
use Drinks\Document\User;

/**
 * Consumption class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 * @ODM\Document(repositoryClass="Drinks\Repository\TransactionRepository")
 */
class Transaction
{
    const CREDIT = 'credit';
    const DEBIT  = 'debit';

    /**
     * @var array
     */
    public static $types = array(self::CREDIT, self::DEBIT);

    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\ReferenceOne(targetDocument="Drinks\Document\User", inversedBy="transactions")
     */
    private $user;

    /**
     * @ODM\String
     */
    private $type;

    /**
     * @ODM/String
     */
    private $label;

    /**
     * @ODM/Date
     */
    private $date;

    /**
     * @PrePersist
     */
    public function prePersist()
    {
        $this->date = new \MongoDate();
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

    /**
     * @param String $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return String
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param String $type
     */
    public function setType($type)
    {
        if (!in_array($type, self::$types)) {
            throw new \InvalidArgumentException("$type is not supported.");
        }

        $this->type = $type;
    }

    /**
     * @return String
     */
    public function getType()
    {
        return $this->type;
    }
}
