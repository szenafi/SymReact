<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this ->faker = Factory::create('fr_FR');  
    }  

    public function load(ObjectManager $manager): void
    {
        for($c = 0; $c < 10; $c++) {
            $customer = new Customer();
            $customer -> setFirstName($this -> faker -> firstName)
                        -> setLastName($this -> faker -> lastName)
                        -> setEmail($this -> faker -> email)
                        -> setCompany($this -> faker -> company);
            $manager -> persist($customer);
        }
        
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
    }