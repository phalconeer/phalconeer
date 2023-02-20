<?php
namespace Phalconeer\Http\Data\Trait;

use Phalconeer\Data;
use Nyholm\Psr7;
use Psr;
use Phalconeer\Http;

trait Message
{
    use Data\Traits\Data\TraitWithProperties;

    /**
    * TODO: add filtering logic
     */
    protected ?\ArrayObject $allowedInHeader = null;
    

    protected ?\ArrayObject $headerVariables;

    /**
    * TODO: add filtering logic
    */
    protected ?\ArrayObject $allowedInBody = null;

    protected ?\ArrayObject $bodyVariables;

    protected string $protocolVersion = Http\Helper\HttpHelper::HTTP_PROTOCOL_VERSION_1_1;

    protected function initializeDataTrait(\ArrayObject $inputObject) : \ArrayObject 
    {
        // if (!$inputObject->offsetExists('allowedInHeader')) {
        //     $inputObject->offsetSet('allowedInHeader', new \ArrayObject());
        // }

        if (!$inputObject->offsetExists('headerVariables')) {
            $inputObject->offsetSet('headerVariables', new \ArrayObject());
        }

        // if (!$inputObject->offsetExists('allowedInBody')) {
        //     $inputObject->offsetSet('allowedInBody', new \ArrayObject());
        // }

        if (!$inputObject->offsetExists('bodyVariables')) {
            $inputObject->offsetSet('bodyVariables', new \ArrayObject());
        }

        return $inputObject;
    }

    public function headerVariables() : array
    {
        /**
         * TODO: Ensure Host is the first header.
         * See: http://tools.ietf.org/html/rfc7230#section-5.4

        */

        return $this->headerVariables->getArrayCopy();
    }

    private function checkHeader() : ?array
    {
        //TODO: throw invalid argument exception when wrong key or value
        //TODO: add header has to create arrays within the internal storage
        return null;
    }

    public function bodyVariableExists($key)
    {
        return $this->bodyVariables->offsetExists($key);
    }

    public function bodyVariable($key)
    {
        if (!$this->bodyVariableExists($key)) {
            return null;
        }

        return $this->bodyVariables->offsetGet($key);
    }

    public function bodyVariables() : array
    {
        return $this->bodyVariables->getArrayCopy();
    }

    public function bodyVariablesToString() : string
    {
        if ($this->bodyVariableExists(Http\Helper\MessageHelper::FULL_TEXT_BODY)) {
            return $this->bodyVariable(Http\Helper\MessageHelper::FULL_TEXT_BODY);
        }

        $iterator = $this->bodyVariables->getIterator();
        $bodyItems = [];
        iterator_apply(
            $iterator,
            function ($iterator, &$bodyItems) {
                $current = $iterator->current();
                if (is_array($current)) {
                    array_map(function ($index) use ($current, $iterator, $bodyItems) {
                        $bodyItems[] = $iterator->key() . '[' . $index . ']=' . urlencode($current[$index]);
                    }, array_keys($current));
                } else {
                    $bodyItems[] = $iterator->key() . '=' . urlencode($iterator->current());
                }
                
                return true;
            },
            [$iterator, &$bodyItems]
        );
        return implode('&', $bodyItems);
    }

    public function withHeaderVariables(array $variables, bool $merge = false) : self
    {
        if ($merge === true) {
            $variables = array_merge_recursive(
                $variables,
                $this->headerVariables->getArrayCopy()
            );
        }

        return $this->setKeyValue(
            'headerVariables',
            new \ArrayObject($variables)
        );
    }

    public function withBodyVariables(array $variables, bool $merge = false) : self
    {
        if (array_key_exists(Http\Helper\MessageHelper::FULL_TEXT_BODY, $variables)) {
            return $this->setBody($variables[Http\Helper\MessageHelper::FULL_TEXT_BODY], $merge);
        }
        
        if ($merge === true) {
            $variables = array_merge_recursive(
                $variables,
                $this->bodyVariables->getArrayCopy()
            );
        }

        return $this->setKeyValue(
            'bodyVariables',
            new \ArrayObject($variables)
        );
    }

    public function setBody(string $body, bool $merge = false) : self
    {
        if ($merge 
            && $this->bodyVariableExists(Http\Helper\MessageHelper::FULL_TEXT_BODY)) {
                $body = $this->bodyVariable(Http\Helper\MessageHelper::FULL_TEXT_BODY) . $body;
            }

        return $this->setKeyValue(
            'bodyVariables',
            new \ArrayObject([
                Http\Helper\MessageHelper::FULL_TEXT_BODY => $body
            ])
        );
    }

    /**
     * per PSR-7 MessageInterface
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->getHeaders() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->getHeaders() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, getHeaders() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return string[][] Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function getHeaders() : array
    {
        $headers = $this->headerVariables();
        return array_map(
            function ($index) use ($headers) {
                return implode(
                    '',
                    [
                        $index,
                        ': ',
                        $headers[$index]
                    ]
                );
            },
            array_keys($headers)
        );
    }

    /**
     * per PSR-7 MessageInterface
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name) : bool
    {
        return $this->headerVariables->offsetExists($name);
    }

    /**
     * per PSR-7 MessageInterface
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function getHeader($name) : array
    {
        if (!$this->hasHeader($name)
            || (!is_null($this->allowedInHeader)
                && !$this->allowedInHeader->offsetExists($name))) {
            return [];
        }
        $return = $this->headerVariables->offsetGet($name);
        if (!is_array($return)) {
            $return = [$return];
        }

        return $return;
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function getHeaderLine($name) : string
    {
        return implode(',', $this->getHeader($name));
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from getHeaders().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header and value.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withHeader($name, $value) : self
    {
        //TODO: lowercase check for the header names, to not have conflicting entries
        return $this->setKeyValue(
            'headerVariables',
            new \ArrayObject([$name => $value])
        );
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return static
     * @throws \InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value) : self
    {
        return $this->withHeaderVariables([$name => $value], true);
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return static
     */
    public function withoutHeader($name) : self
    {
        return $this->setKeyValue(
            'headerVariables',
            new \ArrayObject(
                array_filter(
                    $this->headerVariables(),
                    function ($key) use ($name) {
                        return $key !== $name;
                    },
                    ARRAY_FILTER_USE_KEY
                )
            )
        );
    }


    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody() : Psr\Http\Message\StreamInterface
    {
        return Psr7\Stream::create($this->bodyVariablesToString());
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     */
    public function withBody(Psr\Http\Message\StreamInterface $body) : self
    {
        return $this->setBody($body->getContents());
    }

    public function protocolVersion() : string
    {
        return $this->protocolVersion;
    }

    /**
     * per PSR-7 MessageInterface
     *
     * @return string
     */
    public function getProtocolVersion() : string
    {
        return $this->protocolVersion();
    }

    /**
     * per PSR-7 MessageInterface
     *
     * @param string $version HTTP protocol version
     * @return static
     */
    public function withProtocolVersion($version) : self
    {
        return $this->setKeyValue('protocolVersion', $version);
    }
}