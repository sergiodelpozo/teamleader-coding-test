<?php

use App\Domain\Service\Discount\DiscountCalculator;
use App\Domain\Service\Discount\DiscountCalculatorService;
use App\Domain\Service\Discount\DiscountRules\LoyaltyDiscountRule;
use App\Domain\Service\Discount\DiscountRules\SwitchesBulkDiscountRule;
use App\Domain\Service\Discount\DiscountRules\ToolsCheapestItemDiscountRule;
use App\Domain\Service\Repository\CustomerRepository;
use App\Domain\Service\Repository\DiscountRepository;
use App\Domain\Service\Repository\ProductRepository;
use App\Infrastructure\Controller\Discount\CalculateDiscountController;
use App\Infrastructure\Controller\HealthCheck\HealthCheckController;
use App\Infrastructure\Persistence\MySQL\PDOFactory;
use App\Infrastructure\Persistence\MySQL\Repository\MysqlCustomerRepository;
use App\Infrastructure\Persistence\MySQL\Repository\MysqlDiscountRepository;
use App\Infrastructure\Persistence\MySQL\Repository\MysqlProductRepository;
use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

use function DI\autowire;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container();
$container->set(PDO::class, function (ContainerInterface $c) {
    return PDOFactory::create();
});

$container->set(ProductRepository::class, autowire(MysqlProductRepository::class));
$container->set(CustomerRepository::class, autowire(MysqlCustomerRepository::class));
$container->set(DiscountRepository::class, autowire(MysqlDiscountRepository::class));

$container->set(DiscountCalculatorService::class, function (ContainerInterface $c) {
    return new DiscountCalculator(
        $c->get(LoyaltyDiscountRule::class),
        $c->get(SwitchesBulkDiscountRule::class),
        $c->get(ToolsCheapestItemDiscountRule::class)
    );
});

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$app->get('/healthcheck', HealthCheckController::class);
$app->post('/discounts', CalculateDiscountController::class);

$app->run();
