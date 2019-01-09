<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\Blog;

class CheckArticles extends Command
{
    protected $repository;

    public function __construct(
        \Doctrine\ORM\EntityManagerInterface $em
    )  {
        parent::__construct();

        $this->repository = $em->getRepository(Blog::class);
    }

    protected function configure()
    {
        $this->setName('app:check_articles')
             ->setDescription('Check articles for relevance (set date in format Y-m-d h:m:s)')
             ->setHelp('This command check articles for relevance (set date in format Y-m-d h:m:s)')
             ->addArgument('date', InputArgument::REQUIRED, 'Set actual date');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $oldArticles = $this->repository->getOldArticles($input->getArgument('date'));

        $output->writeln($oldArticles);
    }
}