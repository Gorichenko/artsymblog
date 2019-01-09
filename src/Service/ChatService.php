<?php

namespace App\Service;

use Doctrine\ORM\EntityManager;
use App\Entity\Chat;
use App\Entity\ChatUsers;
use App\Entity\PrivateChat;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ChatService
{
    protected $publicChat;
    protected $privateChat;
    protected $privateChatItems;
    protected $session;
    protected $em;

    public function __construct(
        Chat $publicChat,
        ChatUsers $privateChatItems,
        PrivateChat $privateChat,
        SessionInterface $session,
        EntityManager $em
    )
    {
        $this->publicChat = $publicChat;
        $this->privateChatItems = $privateChatItems;
        $this->privateChat = $privateChat;
        $this->session = $session;
        $this->em = $em;
    }

    public function getPublicChatMessages()
    {
        $messages = $this->em->getRepository('App\Entity\Chat')
            ->getMessages();

        return $messages;
    }

    public function setPublicChatMessages($event)
    {
        if (isset($event['message'])) {
            $publicChat = new Chat();
            $publicChat->setMessage($event['message']);
            $publicChat->setUserId($event['user_id']);
            try {
                $this->em->persist($publicChat);
                $this->em->flush();
            } catch (\Exception $e) {
               return array('error' => $e->getMessage());
            }
        }

        return true;
    }

    public function getPrivateChatMessages($chatId)
    {
        $messages = $this->em->getRepository('App\Entity\ChatUsers')
            ->getMessages($chatId);

        return $messages;
    }

    public function getChat($userFrom, $userTo)
    {
        $chatId = $this->em
            ->getRepository('App\Entity\ChatUsers')
            ->isChatExists($userFrom, $userTo);

        return $chatId;
    }

    public function setPrivateChatMessages($event)
    {
            $chatId = $this->getChat($event['user_from'], $event['user_to']);

            if (empty($chatId)) {
                $chatUsers = new ChatUsers();
                $chatUsers->setUserFrom($event['user_from']);
                $chatUsers->setUserTo($event['user_to']);
                try {
                    $this->em->persist($chatUsers);
                    $this->em->flush();
                } catch (\Exception $e) {

                    return array('error' => $e->getMessage());
                }

                $chatId = $this->getChat($event['user_from'], $event['user_to']);
            }

            if (isset($event['private_message'])) {

                $privateChat = new PrivateChat();
                $currentChat = $this->em->find('App\Entity\ChatUsers', $chatId[0]['id']);
                $privateChat->setChat($currentChat);
                $privateChat->setMessage($event['private_message']);
                $privateChat->setUserId($event['user_from']);
                try {
                    $this->em->persist($privateChat);
                    $this->em->flush();
                } catch (\Exception $e) {

                    return array('error' => $e->getMessage());
                }
            }

            return true;
    }
}
