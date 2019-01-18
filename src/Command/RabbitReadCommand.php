<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use App\Entity\Comment;

class RabbitReadCommand extends ContainerAwareCommand
{
    protected $repository;
    protected $em;

    public function __construct(
        \Doctrine\ORM\EntityManagerInterface $em
    )  {
        parent::__construct();

        $this->em = $em;
        $this->repository = $em->getRepository(Comment::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:getcomment')
            ->setDescription('Get Comment');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new AMQPStreamConnection("rabbit1", 5672, 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();
        $channel->queue_declare('comment_queue', false,false, false, false);

        $callback = function($msg) {
            $request = unserialize($msg->body);
            if (!empty($request)) {
                $comment = new Comment();
                $comment->setBlog($this->getBlog($request['blog_id']));
                $comment->setUser($request['user']);
                $comment->setComment($request['comment']);
                try{
                    $this->em->persist($comment);
                    $this->em->flush();
                    echo "Comment has been saved";
                } catch (\Exception $e) {
                    echo $e->getMessage();
                }
            }
        };

        $channel->basic_consume('comment_queue', '', false, false, false, false, $callback);
        while (count($channel->callbacks)) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }

    protected function getBlog($blog_id)
    {

        $blog = $this->em->getRepository('App\Entity\Blog')->find($blog_id);

        if (!$blog) {
            throw new \Exception('Unable to find Blog post.');
        }

        return $blog;
    }

}