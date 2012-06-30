<?php

namespace Drinks;

use Drinks\Document\Transaction;
use Drinks\Document\Drink;
use Drinks\Document\User;

/**
 * TransactionFactory class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class TransactionFactory
{
    /**
     * @param User $user
     * @param Drink|null $drink
     * @return Document\Transaction
     */
    public function create(User $user, Drink $drink = null)
    {
        $transaction = new \Drinks\Document\Transaction();
        $transaction->setUser($user);

        if (null !== $drink) {
            $transaction->setLabel($drink->getName());
            $transaction->setAmount($drink->getSalePrice());
        }

        return $transaction;
    }

    /**
     * @param User $user
     * @param Drink|null $drink
     * @return Document\Transaction
     */
    public function createCredit(User $user, Drink $drink = null)
    {
        $credit = $this->create($user, $drink);
        $credit->setType(\Drinks\Document\Transaction::CREDIT);

        return $credit;
    }

    /**
     * @param User $user
     * @param Drink|null $drink
     * @return Document\Transaction
     */
    public function createDebit(User $user, Drink $drink = null)
    {
        $debit = $this->create($user, $drink);
        $debit->setType(\Drinks\Document\Transaction::DEBIT);

        return $debit;
    }

    /**
     * @param User $user
     * @param Drink|null $drink
     * @return array
     */
    public function createCompleteTransaction(User $user, Drink $drink = null)
    {
        $credit = $this->createCredit($user, $drink);
        $debit  = $this->createDebit($user, $drink);

        return array($credit, $debit);
    }
}
