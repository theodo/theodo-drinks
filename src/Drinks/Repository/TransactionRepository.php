<?php

namespace Drinks\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Drinks\Document\User;

/**
 * User class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class TransactionRepository extends DocumentRepository
{
    public function findByUser(User $user)
    {
        $query = $this->createQueryBuilder()
            ->field('user.$id')->equals(new \MongoId($user->getId()))
            ->sort('date', 'desc')
            ->getQuery();

        return $query->execute();
    }
}
