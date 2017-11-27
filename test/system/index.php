<?php

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$router = new Hoa\Router\Http();
$router
    ->get(
        'a',
        '/test1',
        function () {
            echo 'Hello, World!';
        }
    );

try {
    $dispatcher = new Hoa\Dispatcher\Basic();
    $dispatcher->dispatch($router);
} catch (\Exception $e) {
    header('HTTP/1.1 404 Not Found');
}
