<?php

namespace greppy;

use greppy\Contracts\ContainerInterface;
use greppy\Contracts\DispatcherInterface;
use greppy\Contracts\RouterInterface;
use greppy\Http\Request;
use greppy\Http\Response;
use greppy\Http\Stream;

class Application
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ContainerInterface $container
     * @return static
     */
    public static function create(ContainerInterface $container): self
    {
        return new self($container);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function handle(Request $request): Response
    {
        $routeMatch = $this->getRouter()->route($request);
        if (!isset($routeMatch)){
            return new Response
            (
                Stream::createFromString(json_encode(["message" => "Route does not exists"])),
                [],
                404
            );
        }
        $response = $this->getDispatcher()->dispatch($routeMatch, $request);

        return $response;
    }

    /**
     * @return RouterInterface
     */
    private function getRouter(): RouterInterface
    {
        return $this->container->get(RouterInterface::class);
    }

    /**
     * @return DispatcherInterface
     */
    private function getDispatcher(): DispatcherInterface
    {
        return $this->container->get(DispatcherInterface::class);
    }
}