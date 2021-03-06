<?php

namespace App\Repository;

use App\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;

/**
 * ChatRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ChatRepository extends \Doctrine\ORM\EntityRepository
{
    public function getMessages()
    {
        $qb = $this->createQueryBuilder('c')
            ->select(['c.user_id', 'c.message']);

        return $qb->getQuery()->getResult();
    }
}
