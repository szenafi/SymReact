<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Invoice;
use App\Entity\Customer;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{ 
   
    /**
     * @var Generator
     */
    private Generator $faker;

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = Factory::create();
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {   
        for($u = 0; $u < 10; $u++) {
            $user = new User();
            $chrono = 1;
            $user ->setFirstName($this->faker->firstName)
                    ->setLastName($this->faker->lastName)
                    ->setEmail($this->faker->email)
                    ->setPassword("password");

                $hashPassword = $this -> hasher -> hashPassword($user, "password");
                $user -> setPassword($hashPassword);

            $manager->persist($user);

        for($c = 0; $c < mt_rand(5, 20); $c++) {
            $customer = new Customer();
            $customer -> setFirstName($this -> faker -> firstName)
                        -> setLastName($this -> faker -> lastName)
                        -> setEmail($this -> faker -> email)
                        -> setCompany($this -> faker -> company)
                        -> setUser($user);

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
        }
        
        
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}