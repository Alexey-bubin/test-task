<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class UserFixtures extends Fixture
{
    private $faker;

    public const USER_REFERENCE = 'user';

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++) {
            $user = new User();
            $user->setName($this->faker->name)
                ->setEmail($this->faker->email)
                ->setPhone($this->faker->phoneNumber);

            $this->addReference(self::USER_REFERENCE . '-' . $i, $user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
