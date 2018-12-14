<?php

namespace ShopBundle\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use ShopBundle\Entity\InitialCash;
use ShopBundle\Entity\OrderStatus;
use ShopBundle\Entity\Role;

class AppFixtures extends Fixture
{
    private const INITIAL_CASH=0.00;

    public function load(ObjectManager $manager)
    {
        $this->addRoles($manager);
        $this->addInitialCash($manager);
        $this->addOrderStatus($manager);
    }

    private function addRoles(ObjectManager $manager){
        $roles = [
            'ROLE_ADMIN',
            'ROLE_EDITOR',
            'ROLE_USER'
        ];

        for ($i = 0; $i < count($roles); $i++) {
            $role = new Role();
            $role->setName($roles[$i]);
            $manager->persist($role);
        }
        $manager->flush();
    }

    private function addInitialCash(ObjectManager $manager)
    {
        $initialCash=new InitialCash();
        $initialCash->setInitialCash(self::INITIAL_CASH);
        $manager->persist($initialCash);
        $manager->flush();
    }

    private function addOrderStatus(ObjectManager $manager){
        $roles = [
            'Open',
            'Pending',
            'Complete'
        ];

        for ($i = 0; $i < count($roles); $i++) {
            $status = new OrderStatus();
            $status->setName($roles[$i]);
            $manager->persist($status);
        }
        $manager->flush();
    }
}