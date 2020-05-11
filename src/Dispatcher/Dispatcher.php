<?php


namespace greppy\Dispatcher;

use greppy\Contracts\ControllerInterface;
use greppy\Contracts\DispatcherInterface;
use greppy\Http\Request;
use greppy\Http\Response;
use greppy\Http\Stream;
use greppy\Routing\RouteMatch;

class Dispatcher implements DispatcherInterface
{
    /**
     * @var string
     */
    private $controllerNameSpace;

    /**
     * @var string
     */
    private $controllerSuffix;

    /**
     * @var array
     */
    private $controlllerList;

    /**
     * Dispatcher constructor.
     * @param string $controllerNameSpace
     * @param string $controllerSuffix
     */
    public function __construct(string $controllerNameSpace, string $controllerSuffix)
    {
        $this->controllerNameSpace = $controllerNameSpace;
        $this->controllerSuffix = $controllerSuffix;
        $this->controlllerList = array();
    }

    /**
     * function injection for controllers;
     * @param ControllerInterface $controller
     */
    public function addController(ControllerInterface $controller)
    {
        $this->controlllerList[] = $controller;
    }

    /**
     * get the full path of controller and return the controller to make action
     * @param RouteMatch $routeMatch
     * @param Request $request
     * @return Response
     */
    public function dispatch(RouteMatch $routeMatch, Request $request): Response
    {
        $fPathController = $this->controllerNameSpace .
            "\\" .
            ucfirst($routeMatch->getControllerName()) .
            $this->controllerSuffix;
        foreach ($this->controlllerList as $item) {
            if (get_class($item) == $fPathController) {
                $controller = $item;

                $actionName = $routeMatch->getActionName();
                $response = $controller->$actionName($request, $routeMatch->getRequestAttributes());

                return $response;
            }
        }

        return new Response(Stream::createFromString(json_encode(["message" => "Route Not Found"])),[],404);
    }
}