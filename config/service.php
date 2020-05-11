<?php

use greppy\Contracts\DispatcherInterface;
use greppy\Contracts\RouterInterface;
use greppy\Controllers\EventController;
use greppy\Controllers\UserController;
use greppy\DependencyInjection\SymfonyContainer;
use greppy\Dispatcher\Dispatcher;
use greppy\Entity\Event;
use greppy\Entity\User;
use greppy\Hashing\HashingService;
use greppy\Hydrator\Hydrator;
use greppy\Repository\EventRepository;
use greppy\Repository\UserRepository;
use greppy\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$config = require("routes.php");
$databaseConfig = require 'db_config.php';

$container = new ContainerBuilder();

$dsn = "mysql:host={$databaseConfig['host']};dbname={$databaseConfig['db']};charset={$databaseConfig['charset']}";

$container->setParameter('configRoutes', $config[Router::CONFIG_ROUTER][Router::CONFIG_ROUTES]);
$container->setParameter('controllerNameSpace', $config[Router::CONFIG_CONTROLLER][Router::CONFIG_CONTROLLER_NAMESPACE]);
$container->setParameter('controllerSuffix', $config[Router::CONFIG_CONTROLLER][Router::CONFIG_CONTROLLER_SUFIX]);
$container->setParameter("PDO", new PDO($dsn, $databaseConfig['user'], $databaseConfig['pass']));

$container->register(RouterInterface::class, Router::class)
    ->addArgument('%configRoutes%');

$container->register(Hydrator::class, Hydrator::class);

$container->register(UserRepository::class, UserRepository::class)
    ->addArgument("%PDO%")
    ->addArgument(User::class)
    ->addArgument(new Reference(Hydrator::class));

$container->register(EventRepository::class, EventRepository::class)
    ->addArgument("%PDO%")
    ->addArgument(Event::class)
    ->addArgument(new Reference(Hydrator::class));

$container->register(HashingService::class, HashingService::class);

$container->register(UserController::class, UserController::class)
    ->addArgument($container->findDefinition(UserRepository::class))
    ->addArgument($container->findDefinition(HashingService::class))
    ->addTag("controller");

$container->register(EventController::class, EventController::class)
    ->addArgument($container->findDefinition(EventRepository::class))
    ->addTag("controller");


$container->register(DispatcherInterface::class, Dispatcher::class)
    ->addArgument('%controllerNameSpace%')
    ->addArgument('%controllerSuffix%');

$dispatcher = $container->findDefinition(DispatcherInterface::class);
foreach ($container->findTaggedServiceIds("controller") as $id => $attributes) {
    $controller = $container->findDefinition($id);
    $dispatcher->addMethodCall("addController", [$controller]);
}
return new SymfonyContainer($container);