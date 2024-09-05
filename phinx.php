<?php

$envFile = '.env';
if ($_ENV['APP_ENV'] == 'production') {
    $envFile = '.env.production';
}
elseif ($_ENV['APP_ENV'] == 'testing') {
    $envFile = '.env.test';
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, $envFile);
$dotenv->load();

return
[
    'paths' => [
        'migrations' => '%%PHINX_CONFIG_DIR%%/src/Infrastructure/Persistence/MySQL/Migration',
        'seeds' => '%%PHINX_CONFIG_DIR%%/src/Infrastructure/Persistence/MySQL/Seed'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'development',
        'production' => [
            'adapter' => 'mysql',
            'host' => $_ENV['MYSQL_HOST'],
            'name' => $_ENV['MYSQL_DB'],
            'user' => $_ENV['MYSQL_USER'],
            'pass' => $_ENV['MYSQL_PASSWORD'],
            'port' => '3306',
            'charset' => $_ENV['MYSQL_CHARSET'] ?? 'utf8',
        ],
        'development' => [
            'adapter' => 'mysql',
            'host' => $_ENV['MYSQL_HOST'],
            'name' => $_ENV['MYSQL_DB'],
            'user' => $_ENV['MYSQL_USER'],
            'pass' => $_ENV['MYSQL_PASSWORD'],
            'port' => '3306',
            'charset' => $_ENV['MYSQL_CHARSET'] ?? 'utf8',
        ],
        'testing' => [
            'adapter' => 'mysql',
            'host' => $_ENV['MYSQL_HOST'],
            'name' => $_ENV['MYSQL_DB'],
            'user' => $_ENV['MYSQL_USER'],
            'pass' => $_ENV['MYSQL_PASSWORD'],
            'port' => '3306',
            'charset' => $_ENV['MYSQL_CHARSET'] ?? 'utf8',
        ]
    ],
    'version_order' => 'creation'
];
