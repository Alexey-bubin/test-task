<?php

namespace App\DataFixtures;

use App\Entity\Partner;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class PartnerFixtures extends Fixture
{
    private $faker;

    public const PARTNER_REFERENCE = 'partners-list';

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++) {
            $partner  = new Partner();
            $partner->setName($this->faker->name);

            $this->addReference(self::PARTNER_REFERENCE . '-' . $i, $partner);
            $manager->persist($partner);
        }

        $manager->flush();
    }
}
