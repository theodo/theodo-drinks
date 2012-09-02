<?php

namespace Drinks\Factory;

use Drinks\Document\Transaction;
use Drinks\Document\Drink;
use Drinks\Document\User;
use Symfony\Component\Translation\Translator;

/**
 * TransactionFactory class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class TransactionFactory
{
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    /**
     * @param  User                 $user
     * @param  Drink|null           $drink
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
     * @param  User                 $user
     * @param  Drink|null           $drink
     * @return Document\Transaction
     */
    public function createCredit(User $user, Drink $drink = null)
    {
        $credit = $this->create($user, $drink);
        $credit->setType(\Drinks\Document\Transaction::CREDIT);

        return $credit;
    }

    /**
     * @param  User                 $user
     * @param  Drink|null           $drink
     * @return Document\Transaction
     */
    public function createDebit(User $user, Drink $drink = null)
    {
        $debit = $this->create($user, $drink);
        $debit->setType(\Drinks\Document\Transaction::DEBIT);

        if ($drink instanceof Drink) {
            $drink->updateQuantity();
        }

        return $debit;
    }

    /**
     * @param  User       $user
     * @param  Drink|null $drink
     * @return array
     */
    public function createCompleteTransaction(User $user, Drink $drink = null)
    {
        $credit = $this->createCredit($user, $drink);
        $debit  = $this->createDebit($user, $drink);

        return array($credit, $debit);
    }

    /**
     * @param User $user
     * @param $amount
     * @return Document\Transaction
     */
    public function createRepayment(User $user, $amount)
    {
        $credit = $this->createCredit($user);
        $credit->setAmount($amount);
        $credit->setLabel($this->translator->trans('Repayment'));

        return $credit;
    }
}
