<?php

use App\Infrastructure\Controller\HealthCheck\HealthCheckController;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$app->get('/healthcheck', HealthCheckController::class);

$app->run();
