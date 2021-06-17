<?php

namespace App\DataFixtures;

use App\Entity\PaymentTypes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class PaymentTypeFixtures extends Fixture
{
    private $faker;

    public const PAYMENT_TYPE_REFERENCE = 'payment-type-list';

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++) {
            $paymentType = new PaymentTypes();
            $paymentType->setName($this->faker->name);

            $this->addReference(self::PAYMENT_TYPE_REFERENCE . '-' . $i, $paymentType);
            $manager->persist($paymentType);
        }

        $manager->flush();
    }
}
