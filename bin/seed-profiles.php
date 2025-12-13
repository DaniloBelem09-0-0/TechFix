#!/usr/bin/env php
<?php

use TechFix\Core\Domain\Entity\Profile;
use TechFix\Core\Domain\Enum\Role;
use TechFix\Infrastructure\Persistence\Doctrine\EntityManagerFactory;

require __DIR__ . '/../vendor/autoload.php';

echo "--- Semeando Profiles Fixos ---\n";

$entityManager = EntityManagerFactory::create();
$profileRepo = $entityManager->getRepository(Profile::class);

// Itera sobre os casos do Enum (Admin, Tech, User)
foreach (Role::cases() as $roleEnum) {
    // Verifica se já existe
    $exists = $profileRepo->findOneBy(['role' => $roleEnum]);
    
    if (!$exists) {
        $profile = new Profile($roleEnum);
        $entityManager->persist($profile);
        echo "Criando profile: " . $roleEnum->value . "\n";
    } else {
        echo "Profile já existe: " . $roleEnum->value . "\n";
    }
}

$entityManager->flush();
echo "✅ Concluído.\n";