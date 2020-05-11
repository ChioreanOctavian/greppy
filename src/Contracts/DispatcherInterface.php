<?php

namespace greppy\Contracts;

use greppy\Http\Request;
use greppy\Http\Response;
use greppy\Routing\RouteMatch;

interface DispatcherInterface
{
    /**
     * Obtains controller based on the RouteMatch and executes its logic/method passing the request.
     *
     * @param RouteMatch $routeMatch
     * @param Request    $request
     *
     * @return Response
     */
    public function dispatch(RouteMatch $routeMatch, Request $request);
}
