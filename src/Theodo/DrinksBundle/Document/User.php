<?php

namespace Theodo\DrinksBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Document\User as BaseUser;

/**
 * User class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 * @ODM\Document(repositoryClass="Theodo\DrinksBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @ODM\Id
     * @var integer
     */
    protected $id;

    /**
     * @ODM\Int
     * @var integer
     */
    protected $balance;

   /**
     * @ODM\ReferenceMany(targetDocument="Theodo\DrinksBundle\Document\Transaction", mappedBy="user")
     * @var ArrayCollection
     */
    protected $transactions;

    /**
     * @ODM\ReferenceMany(targetDocument="Theodo\DrinksBundle\Document\Restocking", mappedBy="users")
     * @var ArrayCollection
     */
    protected $restockings;

    /**
     * Stores the number of drinks consummed by the user.
     *
     * @ODM\Int
     * @var integer
     */
    protected $drinks;

    /**
     * @ODM\Int
     * @var integer
     */
    protected $googleId;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->transactions = new ArrayCollection();
        $this->restockings  = new ArrayCollection();
    }

    /**
     * @param $consumptions
     */
    public function setTransactions($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * @return array|\Doctrine\Common\Collections\ArrayCollection
     */
    public function getTransactions()
    {
        return $this->transactions;
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

    /**
     * @param Integer $amount
     */
    public function debite($amount)
    {
        $this->balance -= $amount;
    }

    /**
     * @param Integer $amount
     */
    public function credite($amount)
    {
        $this->balance += $amount;
    }

    /**
     * @return float
     */
    public function getFormattedBalance()
    {
        return $this->getBalance() / 100;
    }

    /**
     * @return int
     */
    public function getDrinks()
    {
        return $this->drinks;
    }

    /**
     * Add a drink to the count.
     */
    public function addDrink()
    {
        $this->drinks += 1;
    }

    /**
     * Reset the drinks count.
     */
    public function resetDrinks()
    {
        $this->drinks = 0;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $restockings
     */
    public function setRestockings($restockings)
    {
        $this->restockings = $restockings;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRestockings()
    {
        return $this->restockings;
    }

    /**
     * @param int $googleId
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;
    }

    /**
     * @return int
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }
}
