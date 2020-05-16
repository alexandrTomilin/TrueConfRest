<?php
// app/Provider/WebProvider.php

namespace App\Provider;

use App\Controller\HelloController;
use App\Controller\HomeController;
use App\Controller\UserController;
use App\Support\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Twig\Environment;
use UltraLite\Container\Container;

class WebProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        /*
         * Зарегистрируем контроллеры
         */
        $container->set(UserController::class, function (ContainerInterface $container) {
            return new UserController($container->get(Environment::class));
        });

        $container->set(HomeController::class, function (ContainerInterface $container) {
            return new HomeController($container->get(RouteCollectorInterface::class)->getRouteParser());
        });

        /*
         * Зарегистрируем маршруты
         */
        $router = $container->get(RouteCollectorInterface::class);

        $router->group('/', function(RouteCollectorProxyInterface $router) {
            $router->get('', HomeController::class . ':show')->setName('index');
        });

        $router->group('/users', function(RouteCollectorProxyInterface $router) {
            $router->get('', UserController::class . ':show')->setName('users');
            $router->get('/{id}', UserController::class . ':find')->setName('find-user');
            $router->post('/create', UserController::class . ':add')->setName('new-user');
            $router->delete('/remove/{id}', UserController::class . ':delete')->setName('remove-user');
            $router->post('/update/{id}', UserController::class . ':rename')->setName('update-user');
        });
    }
}