#!/usr/bin/env php
<?php

use TechFix\Core\Domain\Entity\User;
use TechFix\Core\Domain\Entity\Profile;
use TechFix\Core\Domain\Enum\Role;
use TechFix\Infrastructure\Persistence\Doctrine\EntityManagerFactory;

require __DIR__ . '/../vendor/autoload.php';

echo "--- Criando Usuário Admin ---\n";

$entityManager = EntityManagerFactory::create();

// 1. Verifica se o Admin já existe
$userRepo = $entityManager->getRepository(User::class);
$existing = $userRepo->findOneBy(['email' => 'admin@techfix.com']);

if ($existing) {
    echo "❌ O Admin já existe no banco!\n";
    exit(1);
}

// 2. BUSCA O PROFILE NO BANCO (CRUCIAL)
// Recuperamos o objeto que o Doctrine já gerencia.
$profileRepo = $entityManager->getRepository(Profile::class);
$adminProfile = $profileRepo->findOneBy(['role' => Role::ADMIN]);

if (!$adminProfile) {
    echo "⚠️ Erro: O Profile 'ADMIN' não foi encontrado no banco.\n";
    echo "Rode 'php bin/seed-profiles.php' primeiro.\n";
    exit(1);
}

try {
    echo "Criando instância do usuário...\n";

    // 3. Cria a entidade User
    // Passamos Role::ADMIN para satisfazer o construtor (caso ele peça Enum ou String)
    $admin = new User(
        "Super Admin",
        "admin@techfix.com",
        "admin123", 
         $adminProfile
    );

    $entityManager->persist($admin);
    $entityManager->flush();

    echo "✅ Sucesso! Admin criado e vinculado ao Profile 'ADMIN' (ID: {$adminProfile->getId()}).\n";
    echo "Email: admin@techfix.com\n";
    echo "Senha: admin123\n";

} catch (\Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}