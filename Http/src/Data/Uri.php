<?php
namespace Phalconeer\Http\Data;

use Psr;
use Phalconeer\Data;
use Phalconeer\Http as This;

/**
 * PSR-7 URI implementation.
 *
 * @author Michael Dowling
 * @author Tobias Schultze
 * @author Matthew Weier O'Phinney
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 * @author Martijn van der Ven <martijn@vanderven.se>
 */
final class Uri extends Data\ImmutableData implements Psr\Http\Message\UriInterface
{
    use Data\Traits\Data\ParseTypes;

    private const SCHEMES = ['http' => 80, 'https' => 443];

    private const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';

    private const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    protected ?string $scheme = '';

    protected ?string $user = '';

    protected ?string $pass = '';

    protected string $host = '';

    protected ?string $port;

    protected ?string $path = '';

    protected ?string $query = '';

    protected ?string $fragment = '';

    public function initializeData(\ArrayObject $inputObject) : \ArrayObject 
    {
        $scheme = null;
        if ($inputObject->offsetExists('scheme')) {
            $inputObject->offsetSet('scheme', $this->convertScheme($inputObject->offsetGet('scheme')));
            $scheme = $this->convertScheme($inputObject->offsetGet('scheme'));
        }
        if ($inputObject->offsetExists('port')) {
            
            $inputObject->offsetSet('port', $this->convertPort($inputObject->offsetGet('port'), $scheme));
        }
        if ($inputObject->offsetExists('host')) {
            $inputObject->offsetSet('host', $this->convertHost($inputObject->offsetGet('host')));
        }
        if ($inputObject->offsetExists('path')) {
            $inputObject->offsetSet('path', $this->convertPath($inputObject->offsetGet('path')));
        }
        if ($inputObject->offsetExists('query')) {
            $inputObject->offsetSet('query', $this->convertQuery($inputObject->offsetGet('query')));
        }
        if ($inputObject->offsetExists('fragment')) {
            $inputObject->offsetSet('fragment', $this->convertFragment($inputObject->offsetGet('fragment')));
        }
        return $inputObject;
    }

    public function scheme(): ?string
    {
        return $this->getValue('scheme');
    }

    public function user(): ?string
    {
        return $this->getValue('user');
    }

    public function pass(): ?string
    {
        return $this->getValue('pass');
    }

    public function host(): string
    {
        return $this->getValue('host');
    }

    public function port(): ?int
    {
        return $this->getValue('port');
    }

    public function path(): ?string
    {
        return $this->getValue('path');
    }

    public function query(): ?string
    {
        return $this->getValue('query');
    }

    public function fragment(): ?string
    {
        return $this->getValue('fragment');
    }

    public function __toString(): string
    {
        return This\Helper\HttpHelper::createUriString($this);
    }

    public function getScheme(): string
    {
        return $this->scheme() ?? '';
    }

    public function getAuthority(): string
    {
        if ($this->host === '') {
            return '';
        }

        $authority = $this->host();
        if ($this->user() !== '') {
            $authority = implode('@', [
                $this->getUserInfo(),
                $authority
            ]);
        }

        $authority = implode(':', array_filter([
            $authority,
            $this->port()
        ]));

        return $authority;
    }

    public function getUserInfo(): string
    {
        if ($this->user() === '') {
            return '';
        }
        
        return implode(':', array_filter(
            [
                $this->user(),
                $this->pass()
            ],
            function ($input) {
                return $input !== '';
            }
        ));
    }

    public function getHost(): string
    {
        return $this->host() ?? '';
    }

    public function getPort(): ?int
    {
        return $this->port();
    }

    public function getPath(): string
    {
        return $this->path() ?? '';
    }

    public function getQuery(): string
    {
        return $this->query() ?? '';
    }

    public function getFragment(): string
    {
        return $this->fragment() ?? '';
    }

    protected function convertScheme(string $scheme = null) : string
    {
        return (is_null($scheme))? '': \strtolower($scheme);
    }

    public function withScheme($scheme): self
    {
        $scheme = $this->convertScheme($scheme);
        if ($this->scheme === $scheme) {
            return $this;
        }
        $new = $this->setValueByKey('scheme', $scheme);
        if (array_key_exists($scheme, This\Helper\HttpHelper::STANDARD_PROTOCOL_PORTS)
            && This\Helper\HttpHelper::STANDARD_PROTOCOL_PORTS[$scheme] === $this->port()) {
            return $new->setValueByKey('port', null);
        }

        return $new;
    }

    public function withUserInfo($user, $password = ''): self
    {
        return $this->setValueByKey('user', $user)
                    ->setValueByKey('pass', $password);
    }

    protected function convertHost(string $host = null) : string
    {
        if (is_null($host))
        {
            return '';
        }
        return \strtolower($host);
    }

    public function withHost($host): self
    {
        $host = $this->convertHost($host);
        if ($this->host === $host) {
            return $this;
        }

        return $this->setValueByKey('host', $host);
    }

    protected function convertPort(int $port = null, string $scheme = null) : ?int
    {
        if (is_null($port)) {
            return $port;
        }
        if (is_null($scheme)) {
            $scheme = $this->scheme;
        }

        if ($port < 0 
            || $port > 0xffff) {
            throw new \InvalidArgumentException(\sprintf('Invalid port: %d. Must be between 0 and 65535', $port));
        }

        if (array_key_exists($scheme, This\Helper\HttpHelper::STANDARD_PROTOCOL_PORTS)
            && This\Helper\HttpHelper::STANDARD_PROTOCOL_PORTS[$scheme] === $port) {
            return null;
        }

        return $port;
    }

    public function withPort($port): self
    {
        $port = $this->convertPort($port);
        if ($this->port() === $port) {
            return $this;
        }

        return $this->setValueByKey('port', $port);
    }

    protected function convertPath(string $path = null) : string
    {
        if (is_null($path))
        {
            return '';
        }
        return This\Helper\HttpHelper::encodePath($path);
    }

    public function withPath($path): self
    {
        $path = $this->convertPath($path);
        if ($this->path === $path) {
            return $this;
        }
        return $this->setValueByKey('path', $path);
    }

    public function withAppendedPath($path): self
    {
        return $this->withPath($this->path . $path);
    }

    protected function convertQuery(string $query = null) : string
    {
        if (is_null($query))
        {
            return '';
        }
        return This\Helper\HttpHelper::encodeQueryOrFragment($query);
    }

    public function withQuery($query): self
    {
        $query = $this->convertQuery($query);
        if ($this->query === $query) {
            return $this;
        }
        return $this->setValueByKey('query', $query);
    }

    public function withQueryVariable(string $key, string $value, $append = true): self
    {
        $query = $this->convertQuery($key . '=' . $value);
        if ($append === true) {
            $query = $this->query .
                    ((empty($this->query)) ? '' : '&') . 
                    $query;
        }
        if ($this->query === $query) {
            return $this;
        }
        return $this->setValueByKey('query', $query);
    }

    protected function convertFragment(string $fragment = null) : string
    {
        if (is_null($fragment))
        {
            return '';
        }
        return This\Helper\HttpHelper::encodeQueryOrFragment($fragment);
    }

    public function withFragment($fragment): self
    {
        if ($this->fragment === $fragment = $this->filterQueryAndFragment($fragment)) {
            return $this;
        }

        $new = clone $this;
        $new->fragment = $fragment;

        return $new;
    }

    public function withUri(string $uri = '')
    {
        if ($uri === '') {
            return new static();
        }
        if (false === $parts = \parse_url($uri)) {
            throw new \InvalidArgumentException("Unable to parse URI: $uri");
        }
        return new static($parts);
    }

    /**
     * Is a given port non-standard for the current scheme?
     */
    private static function isNonStandardPort(string $scheme, int $port): bool
    {
        return !isset(self::SCHEMES[$scheme]) || $port !== self::SCHEMES[$scheme];
    }

    private function filterPath($path): string
    {
        if (!\is_string($path)) {
            throw new \InvalidArgumentException('Path must be a string');
        }

        return \preg_replace_callback('/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/', [__CLASS__, 'rawurlencodeMatchZero'], $path);
    }

    private function filterQueryAndFragment($str): string
    {
        if (!\is_string($str)) {
            throw new \InvalidArgumentException('Query and fragment must be a string');
        }

        return \preg_replace_callback('/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]++|%(?![A-Fa-f0-9]{2}))/', [__CLASS__, 'rawurlencodeMatchZero'], $str);
    }

    private static function rawurlencodeMatchZero(array $match): string
    {
        return \rawurlencode($match[0]);
    }
}