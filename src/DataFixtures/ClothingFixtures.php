<?php
// src/DataFixtures/ClothingFixtures.php

namespace App\DataFixtures;

use App\Entity\Clothing;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClothingFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $clothes = [
            ['name' => 'Blackbelt**', 'price' => 29.90, 'image' => 'blackbelt.jpg'],
            ['name' => 'BlueBelt', 'price' => 29.90, 'image' => 'bluebelt.jpg'],
            ['name' => 'Street', 'price' => 34.50, 'image' => 'street.jpg'],
            ['name' => 'Pokeball**', 'price' => 45.00, 'image' => 'pokeball.jpg'],
            ['name' => 'PinkLady', 'price' => 29.90, 'image' => 'pinklady.jpg'],
            ['name' => 'Snow', 'price' => 32.00, 'image' => 'snow.jpg'],
            ['name' => 'Greyback', 'price' => 28.50, 'image' => 'greyback.jpg'],
            ['name' => 'BlueCloud', 'price' => 45.00, 'image' => 'bluecloud.jpg'],
            ['name' => 'BornInUsa**', 'price' => 59.90, 'image' => 'borninusa.jpg'],
            ['name' => 'GreenSchool', 'price' => 42.20, 'image' => 'greenschool.jpg'],
        ];

        foreach ($clothes as $item) {
            $clothing = new Clothing();

            // Détecte si le nom contient "**" pour le flag highlighted
            $highlighted = false;
            if (str_ends_with($item['name'], '**')) {
                $highlighted = true;
                $item['name'] = str_replace('**', '', $item['name']); // supprime les **
            }

            $clothing->setName($item['name']);
            $clothing->setPrice($item['price']);
            $clothing->setHighlighted($highlighted);
            $clothing->setImage($item['image']); // assigne l'image

            $manager->persist($clothing);
        }

        $manager->flush();
    }

    //public static function getGroups(): array
    //{
    //    return ['clothing'];
    //}
}
