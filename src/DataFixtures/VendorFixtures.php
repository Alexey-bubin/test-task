<?php

namespace App\DataFixtures;

use App\Entity\Vendor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class VendorFixtures extends Fixture
{
    private $faker;

    public const VENDORS_REFERENCE = 'vendors-list';

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++) {
            $vendor  = new Vendor();
            $vendor->setName($this->faker->name);
            $manager->persist($vendor);
            $this->addReference(self::VENDORS_REFERENCE . '-' . $i, $vendor);
        }

        $manager->flush();
    }
}
