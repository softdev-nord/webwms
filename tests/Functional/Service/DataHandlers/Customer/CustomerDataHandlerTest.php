<?php
//
//declare(strict_types=1);
//
//namespace WebWMS\Tests\Functional\Service\DataHandlers\Customer;
//
//use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
//use WebWMS\Entity\Article;
//use WebWMS\Entity\Customer;
//
///**
// * @package:    WebWMS\Tests\Unit\Entity
// * @author:     SoftDev Nord, Rene Irrgang
// * @copyright:  Copyright © 2019-2023, SoftDev Nord
// * Class        CustomerDataHandlerTest.
// *
// * @covers \WebWMS\Service\DataHandlers\Customer\CustomerDataHandler
// */
//final class CustomerDataHandlerTest extends KernelTestCase
//{
//    private EntityManagerInterface $entityManager;
//
//    public function setUp(): void
//    {
//        parent::setUp();
//
//        $kernel = self::bootKernel();
//
//        $this->entityManager = $kernel->getContainer()
//            ->get('doctrine')
//            ->getManager();
//    }
//
//    protected function tearDown(): void
//    {
//        parent::tearDown();
//
//        // doing this is recommended to avoid memory leaks
//        $this->entityManager->close();
//    }
//
//    public function testGetCustomerById(): void
//    {
//        $customerId = 1;
//        $customer = $this->entityManager
//            ->getRepository(Customer::class)
//            ->findOneBy(['customerId' => $customerId]);
//
//        if ($customer !== null) {
//            $this->assertEquals($customerId, $customer->getCustomerId());
//        }
//    }
//
//    public function testGetCustomerByNr(): void
//    {
//        $customerNr = '60000';
//        $customer = $this->entityManager
//            ->getRepository(Customer::class)
//            ->findOneBy(['customerNr' => $customerNr]);
//
//        if ($customer === null) {
//            return;
//        }
//
//        $this->assertEquals($customerNr, $customer->getCustomerNr());
//    }
//
//    public function testGetAllArticles(): void
//    {
//        $articles = $this->entityManager
//            ->getRepository(Article::class)
//            ->findAll();
//
//        $this->assertNotEmpty($articles);
//    }
//}
