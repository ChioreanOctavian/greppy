<?php

use greppy\Routing\Router;

return array(
    Router::CONFIG_CONTROLLER => [
        Router::CONFIG_CONTROLLER_NAMESPACE => "greppy\Controllers",
        Router::CONFIG_CONTROLLER_SUFIX => "Controller"
    ],
    Router::CONFIG_ROUTER => [
        Router::CONFIG_ROUTES => [

            "createUser" => [
                Router::CONFIG_ROUTES_KEY_METHOD => "POST",
                Router::CONFIG_ROUTES_KEY_CONTROLLERNAME => "user",
                Router::CONFIG_ROUTES_KEY_ACTIONNNAME => "createUser",
                Router::CONFIG_ROUTES_KEY_PATH => "/user",
                Router::CONFIG_ROUTES_KEY_REQESTATTRIBUTES => []
            ],

            "auth" => [
                Router::CONFIG_ROUTES_KEY_METHOD => "POST",
                Router::CONFIG_ROUTES_KEY_CONTROLLERNAME => "user",
                Router::CONFIG_ROUTES_KEY_ACTIONNNAME => "authenticate",
                Router::CONFIG_ROUTES_KEY_PATH => "/auth",
                Router::CONFIG_ROUTES_KEY_REQESTATTRIBUTES => []
            ],

            "eventsListing" => [
                Router::CONFIG_ROUTES_KEY_METHOD => "GET",
                Router::CONFIG_ROUTES_KEY_CONTROLLERNAME => "event",
                Router::CONFIG_ROUTES_KEY_ACTIONNNAME => "getEvents",
                Router::CONFIG_ROUTES_KEY_PATH => "/events",
                Router::CONFIG_ROUTES_KEY_REQESTATTRIBUTES => []
            ],

            "newEvent" => [
                Router::CONFIG_ROUTES_KEY_METHOD => "POST",
                Router::CONFIG_ROUTES_KEY_CONTROLLERNAME => "event",
                Router::CONFIG_ROUTES_KEY_ACTIONNNAME => "createEvent",
                Router::CONFIG_ROUTES_KEY_PATH => "/event",
                Router::CONFIG_ROUTES_KEY_REQESTATTRIBUTES => []
            ],

            "updateEvent" => [
                Router::CONFIG_ROUTES_KEY_METHOD => "PUT",
                Router::CONFIG_ROUTES_KEY_CONTROLLERNAME => "event",
                Router::CONFIG_ROUTES_KEY_ACTIONNNAME => "updateEvent",
                Router::CONFIG_ROUTES_KEY_PATH => "/event/{id}",
                Router::CONFIG_ROUTES_KEY_REQESTATTRIBUTES => [
                    "id" => "\d+"
                ]
            ],

            "deleteEvent" => [
                Router::CONFIG_ROUTES_KEY_METHOD => "DELETE",
                Router::CONFIG_ROUTES_KEY_CONTROLLERNAME => "event",
                Router::CONFIG_ROUTES_KEY_ACTIONNNAME => "deleteEvent",
                Router::CONFIG_ROUTES_KEY_PATH => "/event/{id}",
                Router::CONFIG_ROUTES_KEY_REQESTATTRIBUTES => [
                    "id" => "\d+"
                ]
            ],
        ]
    ]
);