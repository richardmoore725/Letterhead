<?php

namespace App\Http;

use Illuminate\Http\JsonResponse;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class Response
{
    private $data;
    private $isError;
    private $isSuccess;
    private $reason;
    private $status;

    /**
     * A map of phrases to optionally use with specific Http
     * status codes.
     *
     * @var array
     */
    private $statusPhrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'We apologize, but something went wrong with your lookup and we weren\'t able to show a list of subscribers. We\'ve sounded the alarms internally and we\'ll address the bug. In the meantime, if you need help, ping us in chat and we\'ll help you finish your task.',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    public function __construct($reason = '', $status = 200, $data = null)
    {
        $this->status = $status;

        $this->data = $data;
        $this->reason = $reason;

        $this->isError = $this->getIsError();
        $this->isSuccess = $this->getIsSuccess();
    }

    public function isClientError(): bool
    {
        $status = $this->getStatus();
        $statusIsHigherThan400 = $status >= 400;
        $statusIsLessThan500 = $status < 500;

        return $statusIsHigherThan400 && $statusIsLessThan500;
    }

    public function isError(): bool
    {
        return $this->isError;
    }

    public function isSuccess(): bool
    {
        return $this->isSuccess;
    }

    /**
     * If the data is just a boolean value, we will return that. Otherwise,
     * return false.
     *
     * @return bool
     */
    public function getBooleanFromResponseBody(): bool
    {
        $responseBody = $this->getData();

        if (is_bool($responseBody)) {
            return $responseBody;
        }

        return false;
    }

    private function getIsError(): bool
    {
        $statusIsLessThan200 = $this->status < 200;
        $statusIsHigherThan300 = $this->status >= 300;
        $statusIsHigherThan500 = $this->status >= 500;

        $isError = $statusIsLessThan200 || $statusIsHigherThan300;

        if ($statusIsHigherThan500) {
            Rollbar::log(Level::ERROR, $this->getReason(), $this->getData());
        }

        return $isError;
    }

    private function getIsSuccess(): bool
    {
        $statusIsAtLeast200 = $this->status >= 200;
        $statusIsLessThan300 = $this->status < 300;

        return $statusIsAtLeast200 && $statusIsLessThan300;
    }

    private function getReason(): string
    {
        if (!empty($this->reason)) {
            return $this->reason;
        }

        return $this->statusPhrases[$this->status];
    }

    public function getStatus(): int
    {
        return $this->status;
    }


    public function getEndUserMessage(): string
    {
        return $this->reason;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getFriendlyErrorMessage()
    {
        return $this->statusPhrases[$this->status];
    }

    public function getJsonResponse(): JsonResponse
    {
        return response()->json($this->getReason(), $this->status);
    }
}
