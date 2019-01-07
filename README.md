# Http Message

Implements [PSR-7](https://www.php-fig.org/psr/psr-7/) PHP Standard.

This library creates a bridge between Swoole and PSR-7 implementation.


## Presentation

Both **Swoole** and **Symfony** for `request` and `response` do not implement PSR-7 which is required to work with PSR-15.

Only **Guzzle**  implements it correctly


## Implementation

### Swoole Server

````php
include 'vendor/autoload.php';

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Woody\Http\Message\ServerRequest;

$server = new Server('0.0.0.0', 9501);

$server->on('request', function (Request $swooleRequest, Response $swooleResponse) {

    $request = ServerRequest::createFromSwoole($swooleRequest);

    // ...
    $response = new \Woody\Http\Message\Response(200, [], 'Hello World');
    $response = $response->withHeader('Server', 'My Server Name');
    // ...

    \Woody\Http\Message\Response::send($response, $swooleResponse);
});

$server->start();
````


### Swoole and Middleware

````php
include 'vendor/autoload.php';

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Woody\Http\Message\ServerRequest;
use Woody\Http\Server\Middleware\Dispatcher;

$server = new Server('0.0.0.0', 9501);

$server->on('request', function (Request $swooleRequest, Response $swooleResponse) {

    $request = ServerRequest::createFromSwoole($swooleRequest);

    $dispatcher = new Dispatcher();
    $dispatcher->pipe(new LogMiddleware());
    $dispatcher->pipe(function($request, $dispatcher) {
        $response = new \Woody\Http\Message\Response(200, [], 'Hello World');
        $response = $response->withHeader('Server', 'My Server Name');
        
        return $response;
    });

    $response = $dispatcher->handle($request);

    \Woody\Http\Message\Response::send($response, $swooleResponse);
});

$server->start();
````