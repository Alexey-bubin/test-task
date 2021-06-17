<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Entity\Vendor;
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
            $order->setDate($this->faker->dateTime);
            $order->setComision($this->faker->randomFloat());

            $partner = $this->getReference(PartnerFixtures::PARTNER_REFERENCE . '-' . random_int(0, 49));
            $order->setPartner($partner);

            $paymentType = $this->getReference(PaymentTypeFixtures::PAYMENT_TYPE_REFERENCE . '-' . random_int(0, 49));
            $order->setPaymentType($paymentType);

            $user = $this->getReference(UserFixtures::USER_REFERENCE . '-' . random_int(0, 49));
            $order->setUser($user);

            $product = $this->getReference(ProductFixtures::PRODUCT_REFERENCE . '-' . random_int(0, 49));

            $orderProducts = new OrderProducts();
            $orderProducts->setCount($this->faker->randomDigit);
            $orderProducts->setOrderNumber($order);
            $orderProducts->setProduct($product);
            $manager->persist($orderProducts);

            $order->setStatus($this->faker->randomElement([0, 1, 2]));

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
            ProductFixtures::class,
            PartnerFixtures::class,
            PaymentTypeFixtures::class,
            UserFixtures::class,
        ];
    }
}
