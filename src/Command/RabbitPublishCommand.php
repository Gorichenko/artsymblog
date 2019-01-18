<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitPublishCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:setmsg')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new AMQPStreamConnection("rabbit1", 5672, 'rabbitmq', 'rabbitmq');
        $channel = $connection->channel();
        $channel->queue_declare('hello', false,false, false, false);
        $msg = new AMQPMessage('Hello' . rand(1111, 99999));
        $channel->basic_publish($msg, '', 'hello');

        echo "[x] Sent 'Hello world!'\n";

        $channel->close();
        $connection->close();
    }
}