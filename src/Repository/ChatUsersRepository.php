<?php

namespace App\Repository;

use App\Entity\Chat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;

/**
 * ChatUsersRepository
 *
 */
class ChatUsersRepository extends \Doctrine\ORM\EntityRepository
{
    public function isChatExists($user_from, $user_to)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.user_from = :user_from')
            ->andWhere('c.user_to = :user_to')
            ->setParameters(['user_from' => $user_from, 'user_to' => $user_to]);

        $result = $qb->getQuery()->getResult();

        if (empty($result)) {

            $qb = $this->createQueryBuilder('c')
                ->select('c.id')
                ->where('c.user_from = :user_from')
                ->andWhere('c.user_to = :user_to')
                ->setParameters(['user_from' => $user_to, 'user_to' => $user_from]);
        }

        return $qb->getQuery()->getResult();
    }

    public function getMessages($chat_id = null)
    {
        $result = '';

        if ($chat_id) {
            $qb = $this->createQueryBuilder('c')
                ->select('p.message, p.user_id, c.id')
                ->leftJoin('c.chats', 'p')
                ->where('c.id = :chat_id')
                ->setParameter('chat_id', $chat_id);

            $result = $qb->getQuery()->getResult();
        }

        return $result;
    }
}
