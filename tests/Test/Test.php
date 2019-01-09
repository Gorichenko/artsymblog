<?php

namespace Tests\Test;

use PHPUnit\Framework\TestCase;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\User;

class Test extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    public $entityManager;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testUserRepo()
    {
        $user = $this->entityManager
            ->getRepository('App\Entity\User')->isUserExist('doe@mail.ru');
        $expect = [0 => ['id' => '9', 'name' => 'john', 'surname' => 'doe', 'email' => 'doe@mail.ru', 'image' => '9-photo.jpeg']];
        $this->assertEquals($expect, $user);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}