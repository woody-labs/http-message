<?php

namespace Woody\Http\Message\Exception;

/**
 * Class NotFoundHttpException
 *
 * @package Woody\Http\Message\Exception
 */
class NotFoundHttpException extends HttpException
{

    /**
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param int        $code     The internal exception code
     * @param array      $headers
     */
    public function __construct(?string $message = null, ?\Exception $previous = null, array $headers = [], ?int $code = 0) {
        parent::__construct(404, $message, $previous, $headers, $code);
    }
}
