<?php

use FastRoute\RouteCollector;
use TechFix\Core\UseCases\Asset\CreateAssetUseCase;
use TechFix\Core\UseCases\Asset\DeleteAssetUseCase;
use TechFix\Infrastructure\Http\Controller\Asset\DeleteAssetController;
use TechFix\Infrastructure\Http\Controller\GithubAuthController;
use TechFix\Infrastructure\Persistence\Doctrine\EntityManagerFactory;
use TechFix\Infrastructure\Persistence\Doctrine\Repository\DoctrineUserRepository;
use TechFix\Infrastructure\Persistence\Doctrine\Repository\DoctrineProfileRepository;
use TechFix\Infrastructure\Persistence\Doctrine\Repository\DoctrineAssetRepository;

use TechFix\Core\UseCases\User\CreateUserUseCase;
use TechFix\Core\UseCases\Login\LoginUseCase;
use TechFix\Core\UseCases\User\FindUserUseCase;
use TechFix\Core\UseCases\Asset\FindAssetUseCase;

use TechFix\Infrastructure\Http\Controller\User\CreateUserController;
use TechFix\Infrastructure\Http\Controller\Login\LoginController;
use TechFix\Infrastructure\Http\Controller\User\FindUserController;
use TechFix\Infrastructure\Http\Controller\Asset\CreateAssetController;
use TechFix\Infrastructure\Http\Controller\Asset\FindAssetController;

use TechFix\Infrastructure\Http\Middleware\AuthMiddleware;
use Symfony\Component\Dotenv\Dotenv; 

require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__ . '/../.env');
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {
    
    $r->addRoute('POST', '/api/login', [
        'controller' => LoginController::class, 
        'roles' => [] 
    ]);

    $r->addRoute('POST', '/api/users', [
        'controller' => CreateUserController::class, 
        'roles' => ['admin'] 
    ]);

    $r->addRoute('GET', '/api/users/{id:\d+}', [
        'controller' => FindUserController::class,
        'roles' => ['admin', 'collaborator'] 
    ]);

    $r->addRoute('GET', '/api/users', [
        'controller' => FindUserController::class,
        'roles' => ['admin'] 
    ]);

    $r->addRoute('POST', '/api/assets', [
        'controller' => CreateAssetController::class,
        'roles' => ['admin', 'technician'] 
    ]);

    // Rotas para encontrar assets (single e lista)
    $r->addRoute('GET', '/api/assets/{id:\d+}', [
        'controller' => FindAssetController::class,
        // ajuste os roles conforme sua regra de negócio
        'roles' => ['admin', 'technician', 'collaborator']
    ]);

    $r->addRoute('GET', '/api/assets', [
        'controller' => FindAssetController::class,
        'roles' => ['admin', 'technician']
    ]);

    $r->addRoute('DELETE', '/api/assets/{id:\d+}', [
        'controller' => DeleteAssetController::class,
        'roles' => ['admin']
    ]);

    $r->addRoute('GET', '/api/auth/github/url', ['controller' => GithubAuthController::class, 'roles' => []]);
    $r->addRoute('GET', '/api/auth/github/callback', ['controller' => GithubAuthController::class, 'roles' => []]);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'Rota não encontrada']);
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
        break;

    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        $controllerClass = $handler['controller']; 
        $requiredRoles = $handler['roles'];        

        if (!empty($requiredRoles)) {
            $middleware = new AuthMiddleware();
            $userData = $middleware->handle($requiredRoles);
        }

        $entityManager = EntityManagerFactory::create();
        $userRepository = new DoctrineUserRepository($entityManager);
        
        if ($controllerClass === CreateUserController::class) {
            $profileRepository = new DoctrineProfileRepository($entityManager);
            $useCase = new CreateUserUseCase($userRepository, $profileRepository);
            $controller = new CreateUserController($useCase);
            $controller->handle();
        }
        else if ($controllerClass === LoginController::class) {
            $useCase = new LoginUseCase($userRepository);
            $controller = new LoginController($useCase);
            $controller->handle();
        }
        else if ($controllerClass === FindUserController::class) {
            $useCase = new FindUserUseCase($userRepository);
            $controller = new FindUserController($useCase);
            $controller->handle($vars, $_GET);
        }
        else if ($controllerClass === FindAssetController::class) {
            $assetRepository = new DoctrineAssetRepository($entityManager);
            $useCase = new FindAssetUseCase($assetRepository);
            $controller = new FindAssetController($useCase);
            $controller->handle($vars, $_GET);
        }
        else if ($controllerClass === CreateAssetController::class) {
            $assetRepository = new DoctrineAssetRepository($entityManager);
            $useCase = new CreateAssetUseCase($assetRepository);
            $controller = new CreateAssetController($useCase);
            $controller->handle();
        }
        else if ($controllerClass === DeleteAssetController::class) {
            $assetRepository = new DoctrineAssetRepository($entityManager);
            $useCase = new DeleteAssetUseCase($assetRepository);
            $controller = new DeleteAssetController($useCase);
            $controller->handle($vars, $_GET);
        }
        if ($controllerClass === GithubAuthController::class) {
            // Requer criar o DoctrineProfileRepository e DoctrineUserRepository antes
            $profileRepo = new DoctrineProfileRepository($entityManager);
            $useCase = new LoginGithubUseCase($userRepository, $profileRepo);
            $controller = new GithubAuthController($useCase);
            
            if (strpos($uri, 'callback') !== false) {
                $controller->callback();
            } else {
                $controller->getUrl();
            }
        }
        break;
}