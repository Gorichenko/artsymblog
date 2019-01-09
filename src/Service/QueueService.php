<?php

namespace App\Service;

use Pheanstalk\Pheanstalk;

class QueueService
{
    private $queue;
    protected $config;
    protected $client;

    public function __construct(array $args)
    {
        $this->config = ['queue' => ['host' => '0.0.0.0']]; // Не обращайте на это внимания. Обычно у менять есть файл конфигурации, но я переместил его сюда специально для целей этой статьи. В качестве хоста используйте IP или Домен.

        $this->queue = $args['queue']; // Просто имя очереди. Я передаю его в качестве параметра. Beanstalkd называет очереди «трубами», но вы можете выбрать любое имя. Если его не существует, оно будет создано.
        $this->client = new Pheanstalk($this->config['queue']['host']); // Инстанцируйте объект.
    }

    public function send($request)
    {
        return $this->client
            ->useTube($this->queue)
            ->put(json_encode($request)); // Отправьте что угодно с кодировкой в формате json – и готово!
    }

    public function listen()
    {
        $this->client->watch($this->queue); // Снова передайте имя очереди.

        while ($job = $this->client->reserve()) { // Продолжайте это делать... чтобы он всегда слушал.
            $message = json_decode($job->getData(), true); // Расшифруйте сообщение

            $status = $this->process($message);

            if ($status)
                $this->client->delete($job);
            else
                $this->client->delete($job); // Удаляйте в любом случае. Позже попытка будет повторена.
        }
    }

    public function process($msg)
    {
        echo $msg;
        return true;
    }
}
