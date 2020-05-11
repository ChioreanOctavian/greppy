<?php

use greppy\Application;
use greppy\Http\Request;

require $baseDir.'./vendor/autoload.php';

// obtain the DI container
$container = require $baseDir.'./config/service.php';

// create the application and handle the request
$application = Application::create($container);
$request = Request::createFromGlobals();
$response = $application->handle($request);
$response->send();
