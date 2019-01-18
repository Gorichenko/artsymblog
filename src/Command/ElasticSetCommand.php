<?php

namespace App\Command;

use Elasticsearch\ClientBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Blog;

class ElasticSetCommand extends ContainerAwareCommand
{
    protected $repository;

    public function __construct(
        EntityManagerInterface $em,
        ?string $name = null
    )
    {
        parent::__construct($name);
        $this->repository = $em->getRepository('App\Entity\Blog');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('elastic:setdata');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $articles = $this->repository->getLatestBlogs();

        foreach ($articles as $article) {
            $client = ClientBuilder::create()
                ->setHosts(['elasticsearch'])
                ->build();

            $params = [
                "index" => "blog",
                "type"  => "article",
                "id" => $article->getId(),
                "body"  => [
                    "title"  => $article->getTitle(),
                    "author" => $article->getAuthor(),
                    "text"  => $article->getBlog(),
                    "created" => $article->getCreated(),
                    "updated" => $article->getUpdated(),
                    "tags"  => $article->getTags()
                ]
            ];

            $response = $client->index($params);
            $output->writeln('Set article ID= ' . $article->getId());

        }

    }
}