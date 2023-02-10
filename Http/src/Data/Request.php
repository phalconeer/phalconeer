<?php
namespace Phalconeer\Http\Data;

use Phalconeer\Id;
use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Http as This;
use Psr;

class Request extends Data\ImmutableData implements Psr\Http\Message\RequestInterface
{
    use This\Data\Traits\Message,
        Data\Traits\Data\ParseTypes,
        Dto\Traits\ArrayLoader;

    protected string $method = This\Helper\HttpHelper::HTTP_METHOD_GET;

    protected string $protocol = This\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL;

    protected ?string $requestId;

    protected ?string $requestTarget;

    protected This\Data\Uri $url;

    protected string $userAgent = 'API';

    public function initializeData(\ArrayObject $inputObject): \ArrayObject
    {
        $inputObject = $this->initializeDataTrait($inputObject);
        if (!$inputObject->offsetExists('requestId')
            || is_null($inputObject->offsetGet('requestId'))) {
            $inputObject->offsetSet('requestId', Id\Helper\IdHelper::generateWithDayPrefix(12));
        }

        return $inputObject;
    }

    public function url() : Psr\Http\Message\UriInterface
    {
        return $this->getValue('url');
    }

    public function requestId() : ?string
    {
        return $this->requestId;
    }

    public function requestTarget() : string
    {
        if (is_null($this->requestTarget)) {
            return implode('?', array_filter([
                $this->url->getPath(),
                $this->url->getQuery()
            ]));
        }

        return $this->requestTarget;
    }

    public function method() : string
    {
        return $this->method;
    }

    /**
     * Retrieves the message's request target.
     *
     * Retrieves the message's request-target either as it will appear (for
     * clients), as it appeared at request (for servers), or as it was
     * specified for the instance (see withRequestTarget()).
     *
     * In most cases, this will be the origin-form of the composed URI,
     * unless a value was provided to the concrete implementation (see
     * withRequestTarget() below).
     *
     * If no URI is available, and no request-target has been specifically
     * provided, this method MUST return the string "/".
     */
    public function getRequestTarget() : string
    {
        return $this->requestTarget();
    }

    /**
     * Return an instance with the specific request-target.
     *
     * If the request needs a non-origin-form request-target — e.g., for
     * specifying an absolute-form, authority-form, or asterisk-form —
     * this method may be used to create an instance with the specified
     * request-target, verbatim.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request target.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (for the various
     *     request-target forms allowed in request messages)
     */
    public function withRequestTarget($requestTarget) : self
    {
        return $this->setValueByKey('requestTarget', $requestTarget);
    }

    /**
     * Retrieves the HTTP method of the request.
     */
    public function getMethod() : string
    {
        return $this->method();
    }

    /**
     * Return an instance with the provided HTTP method.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * changed request method.
     *
     * @param string $method Case-sensitive method.
     * @return static
     */
    public function withMethod($method)
    {
        return $this->setValueByKey('method', $method);
    }

    /**
     * Retrieves the URI instance.
     *
     * This method MUST return a UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     */
    public function getUri() : Psr\Http\Message\UriInterface
    {
        return $this->getValue('url');
    }

    /**
     * Returns an instance with the provided URI.
     *
     * This method MUST update the Host header of the returned request by
     * default if the URI contains a host component. If the URI does not
     * contain a host component, any pre-existing Host header MUST be carried
     * over to the returned request.
     *
     * You can opt-in to preserving the original state of the Host header by
     * setting `$preserveHost` to `true`. When `$preserveHost` is set to
     * `true`, this method interacts with the Host header in the following ways:
     *
     * - If the Host header is missing or empty, and the new URI contains
     *   a host component, this method MUST update the Host header in the returned
     *   request.
     * - If the Host header is missing or empty, and the new URI does not contain a
     *   host component, this method MUST NOT update the Host header in the returned
     *   request.
     * - If a Host header is present and non-empty, this method MUST NOT update
     *   the Host header in the returned request.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new UriInterface instance.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param bool $preserveHost Preserve the original state of the Host header.
     * @return static
     */
    public function withUri(Psr\Http\Message\UriInterface $uri, $preserveHost = false)
    {
        //TODO: preserveHost
        $this->setValueByKey('url', $uri);
    }

    private function updateHostFromUri(): void
    {
        if ('' === $host = $this->url->getHost()) {
            return;
        }

        if (null !== ($port = $this->url->getPort())) {
            $host .= ':' . $port;
        }
    }
}