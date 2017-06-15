<?php

$container = require __DIR__ . '/../app/bootstrap.php';

$request = \Zend\Diactoros\ServerRequestFactory::fromGlobals();

$response = $container->get(\Cocktales\Application\Http\HttpServer::class)->handle($request);

$container->get(\Zend\Diactoros\Response\SapiEmitter::class)->emit($response);

