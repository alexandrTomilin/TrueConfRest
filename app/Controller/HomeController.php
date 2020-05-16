<?php
// app/Controller/HomeController.php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;

class HomeController
{

    /**
     * @var RouteParserInterface
     */
    private $router;

    public function __construct(RouteParserInterface $router)
    {
        $this->router = $router;
    }

    public function show(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $response
            ->withStatus(301)
            ->withHeader('location', '/users');
    }
}