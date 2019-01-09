<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PrivateChat
 *
 * @ORM\Entity(repositoryClass="App\Repository\PrivateChatRepository")
 * @ORM\Table(name="private_chat")
 * @ORM\HasLifecycleCallbacks
 */
class PrivateChat
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $chat_id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $user_id;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $message;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\ManyToOne(targetEntity="ChatUsers", inversedBy="chats")
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id")
     */
    protected $chat;

    /**
     * Set chat_id.
     *
     * @param int $chat_id
     *
     * @return PrivateChat
     */
    public function setChatId($chat_id)
    {
        $this->chat_id = $chat_id;

        return $this;
    }

    /**
     * Get chat_id.
     *
     * @return int
     */
    public function getChatId()
    {
        return $this->chat_id;
    }

    /**
     * Set user_id.
     *
     * @param int $user_id
     *
     * @return PrivateChat
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Get user_id.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set message.
     *
     * @param string $message
     *
     * @return PrivateChat
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set chat.
     *
     * @param \App\Entity\ChatUsers|null $chat
     *
     * @return PrivateChat
     */
    public function setChat(\App\Entity\ChatUsers $chat = null)
    {
        $this->chat = $chat;

        return $this;
    }

    /**
     * Get chat.
     *
     * @return \App\Entity\ChatUsers|null
     */
    public function getChat()
    {
        return $this->chat;
    }
}
