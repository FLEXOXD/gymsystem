<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$request = Illuminate\Http\Request::create('/superadmin/plans', 'GET');
$route = $app->make('router')->getRoutes()->match($request);
echo 'ROUTE_NAME=' . ($route->getName() ?? 'null') . PHP_EOL;
echo 'URI=' . $route->uri() . PHP_EOL;
echo 'MIDDLEWARE=' . implode(',', $route->gatherMiddleware()) . PHP_EOL;