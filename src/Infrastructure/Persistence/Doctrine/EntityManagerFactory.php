<?php

namespace TechFix\Infrastructure\Persistence\Doctrine;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Dotenv\Dotenv;

class EntityManagerFactory
{
    public static function create(): EntityManager
    {
        $dotenv = new Dotenv();

        $dotenv->load(__DIR__ . '/../../../../.env'); 

        $paths = [__DIR__ . '/../../../Core/Domain/Entity'];
        $isDevMode = true;

        $config = ORMSetup::createAttributeMetadataConfiguration($paths, $isDevMode);

        $connectionParams = [
            'dbname'   => $_ENV['DB_NAME'],
            'user'     => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'],
            'host'     => $_ENV['DB_HOST'],
            'driver'   => 'pdo_mysql',
        ];

        $connection = DriverManager::getConnection($connectionParams, $config);

        return new EntityManager($connection, $config);
    }
}