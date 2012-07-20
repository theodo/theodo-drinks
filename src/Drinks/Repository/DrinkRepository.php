<?php

namespace Drinks\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * User class.
 *
 * @author Benjamin Grandfond <benjamin.grandfond@gmail.com>
 */
class DrinkRepository extends DocumentRepository
{
    /**
     * Find all available drinks.
     *
     * @return bool|\Doctrine\MongoDB\ArrayIterator|\Doctrine\MongoDB\Cursor|\Doctrine\MongoDB\EagerCursor|mixed|null
     */
    public function findAvailables()
    {
        return $this->createQueryBuilder()
            ->field('quantity')->gt(0)
            ->getQuery()
            ->execute();
    }

    /**
     * Find drinks by ids.
     *
     * @param array $ids
     * @return bool|\Doctrine\MongoDB\ArrayIterator|\Doctrine\MongoDB\Cursor|\Doctrine\MongoDB\EagerCursor|mixed|null
     */
    public function findByIds(array $ids)
    {
        return $this->createQueryBuilder()
            ->field('_id')->in($ids)
            ->getQuery()
            ->execute();
    }

}
