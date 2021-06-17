<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;

    public const PRODUCT_REFERENCE = 'product-list';

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++) {
            $product  = new Product();
            $product->setName($this->faker->name);
            $product->setPrice($this->faker->randomFloat());
            $product->setSku($this->faker->randomNumber());

            $vendor = $this->getReference(VendorFixtures::VENDORS_REFERENCE . '-' . random_int(0, 49));
            $product->setVendor($vendor);
            $this->addReference(self::PRODUCT_REFERENCE . '-' . $i, $product);

            $manager->persist($product);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies():array
    {
        return [
            VendorFixtures::class
        ];
    }
}
