<?php

namespace greppy\Contracts;


use greppy\Http\Request;
use greppy\Routing\RouteMatch;

interface RouterInterface
{
    public function route(Request $request): ?RouteMatch;
}
