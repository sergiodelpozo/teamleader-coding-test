<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\HealthCheck;

use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class HealthCheckController
{
    public function __invoke(Request $request, Response $response): Response
    {
        $response->getBody()->write('OK');

        return $response;
    }
}
