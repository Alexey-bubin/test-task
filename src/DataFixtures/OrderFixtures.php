<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderProducts;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10000; $i++) {
            $order  = new Order();

            $partner     = $this->getReference(PartnerFixtures::PARTNER_REFERENCE . '-' . random_int(0, 49));
            $paymentType = $this->getReference(PaymentTypeFixtures::PAYMENT_TYPE_REFERENCE . '-' . random_int(0, 49));
            $user        = $this->getReference(UserFixtures::USER_REFERENCE . '-' . random_int(0, 49));
            $vendor      = $this->getReference(VendorFixtures::VENDORS_REFERENCE . '-' . random_int(0, 49));

            $order->setDate($this->faker->dateTime)
                ->setComision($this->faker->randomFloat())
                ->setPartner($partner)
                ->setPaymentType($paymentType)
                ->setUser($user)
                ->setCount($this->faker->randomElement([0, 1, 2]))
                ->setProductName($this->faker->name)
                ->setPrice($this->faker->randomFloat())
                ->setSku($this->faker->randomNumber())
                ->setVendor($vendor)
                ->setStatus($this->faker->randomElement([0, 1, 2]));

            $manager->persist($order);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies():array
    {
        return [
            VendorFixtures::class,
            PartnerFixtures::class,
            PaymentTypeFixtures::class,
            UserFixtures::class,
        ];
    }
}
