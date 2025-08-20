<?php

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;

require __DIR__.'/vendor/autoload.php';

$kernel = new \App\Kernel('dev', true);
$kernel->boot();

$entityManager = $kernel->getContainer()->get('doctrine')->getManager();
$passwordHasher = $kernel->getContainer()->get(UserPasswordHasherInterface::class);

$admin = new User();
$admin->setEmail('test@example.com');
$admin->setRoles(['ROLE_ADMIN']);
$admin->setPassword($passwordHasher->hashPassword($admin, 'password123'));

$entityManager->persist($admin);
$entityManager->flush();

echo "Admin créé avec succès\n";
