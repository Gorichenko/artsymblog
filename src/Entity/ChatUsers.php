<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ChatUsers
 *
 * @ORM\Entity(repositoryClass="App\Repository\ChatUsersRepository")
 * @ORM\Table(name="chat_users")
 * @ORM\HasLifecycleCallbacks
 */
class ChatUsers
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
    protected $user_from;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $user_to;

    /**
     * @ORM\OneToMany(targetEntity="PrivateChat", mappedBy="chat")
     */
    protected $chats;

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
     * Get user_from.
     *
     * @return int
     */
    public function getUserFrom()
    {
        return $this->user_from;
    }

    /**
     * Set user_from.
     *
     * @param int $user_from
     *
     * @return ChatUsers
     */
    public function setUserFrom($user_from)
    {
        $this->user_from = $user_from;

        return $this;
    }

    /**
     * Get user_to.
     *
     * @return int
     */
    public function getUserTo()
    {
        return $this->user_to;
    }

    /**
     * Set user_to.
     *
     * @param int $user_to
     *
     * @return ChatUsers
     */
    public function setUserTo($user_to)
    {
        $this->user_to = $user_to;

        return $this;
    }

    public function getChats()
    {
        return $this->chats;
    }
}
