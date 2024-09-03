<?php

use App\Infrastructure\Controller\HealthCheck\HealthCheckController;
use App\Infrastructure\Persistence\MySQL\PDOFactory;
use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container();
$container->set(PDO::class, function (ContainerInterface $c) {
    return PDOFactory::create();
});

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$app->get('/healthcheck', HealthCheckController::class);

$app->run();
