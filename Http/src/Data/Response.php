<?php
namespace Phalconeer\Http\Data;

use Phalconeer\Data;
use Phalconeer\Http as This;
use Psr\Http\Message\ResponseInterface;

class Response extends Data\ImmutableData implements ResponseInterface
{
    use This\Data\Traits\Message,
        Data\Traits\Data\ParseTypes;

    protected int $statusCode;

    protected string $reasonPhrase;

    protected string $requestId;

    public function requestId() : ?string
    {
        return $this->getValue('requestId');
    }

    public function statusCode() : int
    {
        return $this->getValue('statusCode');
    }

    public function getStatusCode(): int
    {
        return $this->getValue('statusCode');
    }

    public function getReasonPhrase(): string
    {
        if (!is_null($this->reasonPhrase)) {
            return $this->reasonPhrase;
        }
        if (!array_key_exists($this->statusCode, This\Helper\HttpHelper::RESPONSE_MEANINGS)) {
            return '';
        }
        return This\Helper\HttpHelper::RESPONSE_MEANINGS[$this->statusCode];
    }

    public function withStatus($code, $reasonPhrase = null) : self
    {
        $code = (int) $code;
        if ($code < 100 || $code > 599) {
            throw new \InvalidArgumentException('Status code has to be an integer between 100 and 599');
        }
        if (is_null($reasonPhrase) && array_key_exists($code, This\Helper\HttpHelper::RESPONSE_MEANINGS)) {
            $reasonPhrase = This\Helper\HttpHelper::RESPONSE_MEANINGS[$code];
        }

        return $this->setValueByKey('statusCode', $code)
                ->setValueByKey('reasonPhrase', $reasonPhrase);
    }

    public function setRequestId(string $requestId) : self
    {
        return $this->setValueByKey('requestId', $requestId);
    }
}