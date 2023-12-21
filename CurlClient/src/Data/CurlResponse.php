<?php
namespace Phalconeer\CurlClient\Data;

use Psr;
use Phalconeer\CurlClient as This;
use Phalconeer\Http;

class CurlResponse implements Psr\Http\Message\ResponseInterface, Http\MessageInterface
{
    protected Http\Data\Response $response;

    public function __construct(Http\Data\Response $response = null)
    {
        if (is_null($response)) {
            $response = new Http\Data\Response();
        }
        $this->response = $response;
    }

    public function readHeaderStream() : callable
    {
        return function ($ch, $data) {
            $str = trim($data);
            if ('' !== $str) {
                if (0 === strpos(strtolower($str), 'http/')) {
                    $parts = explode(' ', $str, 3);
                    if (\count($parts) < 2 || 0 !== strpos(strtolower($parts[0]), 'http/')) {
                        throw new This\Exception\InvalidHttpStatusException($str, This\Helper\ExceptionHelper::CURL_CLIENT_RESPONSE__INVALID_STATUS_CODE);
                    }
                    $this->response = $this->response->withProtocolVersion((string) substr($parts[0], 5));
                    $this->response = $this->response->withStatus((int) $parts[1], isset($parts[2]) ? $parts[2] : null);
                } else {
                    list($key, $value) = explode(':', $str, 2);
                    $this->response = $this->response->withAddedHeader(trim($key), trim($value));
                }
            }

            return \strlen($data);
        };
    }

    public function readBodyStream() : callable
    {
        return function ($ch, string $data) {
            $this->response = $this->response->setBody($data, true);
            return strlen($data);
        };
    }

    public function getProtocolVersion() : string
    {
        $this->response->getProtocolVersion();
    }

    public function withProtocolVersion($version) : Psr\Http\Message\ResponseInterface
    {
        $this->response = $this->response->withProtocolVersion($version);
        return $this->response;
    }

    public function getHeaders() : array
    {
        return $this->response->getHeaders();
    }

    public function hasHeader(string $name) : bool
    {
        $this->response = $this->response->hasHeader($name);
        return $this->response;
    }

    public function getHeader(string $name) : array
    {
        return $this->response->getHeader($name);
    }

    public function getHeaderLine(string $name) : string
    {
        return $this->getHeaderLine($name);
    }

    public function withHeader(string $name, $value) : Psr\Http\Message\MessageInterface
    {
        $this->response = $this->response->withHeader($name, $value);
        return $this->response;
    }

    public function withAddedHeader(string $name, $value) : Psr\Http\Message\MessageInterface
    {
        $this->response = $this->response->withAddedHeader($name, $value);
        return $this->response;
    }

    public function withoutHeader(string $name) : Psr\Http\Message\MessageInterface
    {
        $this->response = $this->response->withoutHeader($name);
        return $this->response;
    }

    public function getBody() : Psr\Http\Message\StreamInterface
    {
        return $this->response->getBody();
    }

    public function withBody(Psr\Http\Message\StreamInterface $body) : Psr\Http\Message\MessageInterface
    {
        $this->response = $this->response->withBody($body);
        return $this->response;
    }

    public function getStatusCode() : int
    {
        return $this->response->getStatusCode();
    }

    public function withStatus(int $code, string $reasonPhrase = '') : Psr\Http\Message\ResponseInterface
    {
        $this->response = $this->response->withStatus($code, $reasonPhrase);
        return $this->response;
    }

    public function getReasonPhrase() : string
    {
        $this->response->getReasonPhrase();
    }

    // From the Message trait
    public function bodyVariable(string $key)
    {
        return $this->response->bodyVariable($key);
    }

    public function bodyVariables() : array
    {
        return $this->response->bodyVariables();
    }

    public function bodyVariableExists(string $key) : bool
    {
        return $this->response->bodyVariableExists($key);
    }

    public function withBodyVariables(array $variables, bool $merge = false) : self
    {
        return $this->response->withBodyVariables($variables, $merge);
    }
}