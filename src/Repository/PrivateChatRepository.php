<?php

namespace App\Repository;

use App\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;

/**
 * PrivateChatRepository
 *
 */
class PrivateChatRepository extends \Doctrine\ORM\EntityRepository
{
    public function getMessagesCount($user_id)
    {
        $qb = $this->createQueryBuilder('p')
            ->select(['p.chat_id', 'count(p.message) as count'])
            ->where('p.user_id != :user_id')
            ->setParameter('user_id', $user_id)
            ->groupBy('p.chat_id');

        return $qb->getQuery()->getResult();
    }

    public function getChatMessagesCount($user_id, $chat_id)
    {
        $qb = $this->createQueryBuilder('p')
            ->select(['p.chat_id', 'count(p.message) as count'])
            ->where('p.chat_id = :chat_id')
            ->andWhere('p.user_id != :user_id')
            ->setParameters(['chat_id' => $chat_id, 'user_id' => $user_id]);

        return $qb->getQuery()->getResult();
    }
}
