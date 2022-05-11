<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Invoice;
use App\Entity\Customer;
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
    {   $chrono = 1;
        for($c = 0; $c < 10; $c++) {

            
            $customer = new Customer();
            $customer -> setFirstName($this -> faker -> firstName)
                        -> setLastName($this -> faker -> lastName)
                        -> setEmail($this -> faker -> email)
                        -> setCompany($this -> faker -> company);
            $manager -> persist($customer);

        for($i = 0; $i < 10; $i++) {
            $invoice = new Invoice();
            $invoice -> setAmount($this -> faker -> randomFloat(2, 0, 100))
                    -> setSentAt($this -> faker -> dateTimeBetween('-1 years', 'now'))
                    ->setStatus($this -> faker -> randomElement(['SENT', 'PAID', 'CANCELED']))
                    ->setCustomer($customer)
                    ->setChrono($chrono);
            
            $manager -> persist($invoice);
            $chrono++;
        }
        }
        
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
    }