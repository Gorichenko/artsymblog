<?php

namespace App\Service;

use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Chat;
use App\Entity\ChatUsers;
use App\Entity\PrivateChat;
use Doctrine\ORM\EntityManager;

class TopicService implements TopicInterface
{
    protected $currentChat = null;
    protected $session;
    protected $chat;
    protected $chatUsers;
    protected $privateChat;
    protected $em;

    public function __construct(
        EntityManager $em,
        Chat $chat,
        ChatUsers $chatUsers,
        PrivateChat $privateChat,
        SessionInterface $session
    )
    {
        $this->session = $session;
        $this->chat = $chat;
        $this->chatUsers = $chatUsers;
        $this->privateChat = $privateChat;
        $this->em = $em;
    }

    public function getChatService()
    {
        if (null === $this->currentChat) {
            $this->currentChat = new ChatService(
                $this->chat,
                $this->chatUsers,
                $this->privateChat,
                $this->session,
                $this->em
            );
        }

        return $this->currentChat;
    }

    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $chatId = $request->getAttributes()->get('chat_id');

        if ($chatId != 0) {
            $messages = $this->getChatService()->getPrivateChatMessages($chatId);
        } else {
            $messages = $this->getChatService()->getPublicChatMessages();
        }

        $topic->broadcast([
            'msg' => json_encode($messages),
        ]);

    }

    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $room = $request->getAttributes()->get('room');
        $userId = $request->getAttributes()->get('chat_id');
        //this will broadcast the message to ALL subscribers of this topic.
//        $topic->broadcast([
//            'msg' => json_encode(['disconnect' => 'Новый пользователь вышел из комнаты'])
//        ]);
    }

    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        $chatId = $request->getAttributes()->get('chat_id');

        if (isset($event['message'])) {
            $this->getChatService()->setPublicChatMessages($event);
        }

        if (isset($event['private_message'])) {
            $this->getChatService()->setPrivateChatMessages($event);
        }

        if ($chatId != 0) {
            $messages = $this->getChatService()->getPrivateChatMessages($chatId);
        } else {
            $messages = $this->getChatService()->getPublicChatMessages();
        }

        $topic->broadcast([
            'msg' => json_encode($messages),
        ]);
    }

    public function getName()
    {
        return 'app.topic.chat';
    }
}
