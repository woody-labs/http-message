# Http Message

Implements [PSR-7](https://www.php-fig.org/psr/psr-7/) PHP Standard.


## Presentation

Both **Swoole** and **Symfony**, for `request` and `response` objects, do not implement PSR-7 which is required to work with PSR-15.

Only **Guzzle**  implements it correctly.

This library creates a bridge between Swoole and PSR-7 implementation.


## Implementation

### Swoole Server

````php
include 'vendor/autoload.php';

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;
use Woody\Http\Message\ServerRequestFactory;
use Woody\Http\Message\ResponseSender;

$serverRequestFactory = new ServerRequestFactory();
$responseSender = new ResponseSender();

$server = new Server('0.0.0.0', 9501);
$server->on('request', function (Request $swooleRequest, Response $swooleResponse) use ($serverRequestFactory, $responseSender) {

    $request = $serverRequestFactory->create($swooleRequest);

    // ...
    $response = new \Zend\Diactoros\Response\HtmlResponse('Hello World', 200);
    $response = $response->withHeader('Server', 'My Server Name');
    // ...

    $responseSender->send($response, $swooleResponse);
});
$server->start();
````
