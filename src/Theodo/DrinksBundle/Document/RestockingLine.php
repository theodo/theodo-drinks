<?php

namespace Theodo\DrinksBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Theodo\DrinksBundle\Document\Drink;

/**
 * RestockingLine class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 * @ODM\EmbeddedDocument
 */
class RestockingLine
{
    /**
     * @ODM\Id
     */
    private $id;

    /**
     * @ODM\ReferenceOne(targetDocument="Theodo\DrinksBundle\Document\Drink", cascade={"persist"})
     */
    private $drink;

    /**
     * @ODM\Int
     */
    private $quantity;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Theodo\DrinksBundle\Document\Drink $drink
     */
    public function setDrink(Drink $drink)
    {
        $this->drink = $drink;
    }

    /**
     * @return Theodo\DrinksBundle\Document\Drink
     */
    public function getDrink()
    {
        return $this->drink;
    }

    /**
     * @param integer $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @ODM\PrePersist
     */
    public function prePersist()
    {
        $this->drink->updateQuantity($this->quantity);
    }
}
