<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class MessageException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = HttpResponse::HTTP_BAD_REQUEST,
    ) {
        parent::__construct($message, $code);
    }

    public function getStatusCode(): int
    {
        return (int) $this->getCode();
    }
}
