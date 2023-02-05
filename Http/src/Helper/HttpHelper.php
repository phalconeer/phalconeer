<?php
namespace Phalconeer\Http\Helper;

use Phalconeer\Http as This;

class HttpHelper
{
    const HTTP_METHOD_GET       = 'GET';

    const HTTP_METHOD_POST      = 'POST';

    const HTTP_METHOD_PUT       = 'PUT';

    const HTTP_METHOD_PATCH     = 'PATCH';

    const HTTP_METHOD_DELETE    = 'DELETE';

    const HTTP_METHOD_OPTIONS   = 'OPTIONS';

    const HTTP_METHOD_HEAD      = 'HEAD';

    const HTTP_PROTOCOL_NORMAL  = 'http';
    
    const HTTP_PROTOCOL_SECURE  = 'https';

    const HTTP_PROTOCOL_VERSION_1_0 = '1.0';

    const HTTP_PROTOCOL_VERSION_1_1 = '1.1';

    const HTTP_PROTOCOL_VERSION_2_0 = '2.0';

    const VARIABLES_ENCODE_JSON = 'json';

    const VARIABLES_ENCODE_FORM = 'form-urlencoded';

    const VARIABLES_ENCODE_X_WWW_FORM_URLENCODED = 'x-www-form-urlencoded';
 
    const AUTHENTICATION_BASIC  = 'basic';
    
    const AUTHENTICATION_BEARER = 'bearer';

    const VALID_METHODS = [
        self::HTTP_METHOD_GET,
        self::HTTP_METHOD_POST,
        self::HTTP_METHOD_PUT,
        self::HTTP_METHOD_PATCH,
        self::HTTP_METHOD_DELETE,
        self::HTTP_METHOD_OPTIONS,
        self::HTTP_METHOD_HEAD,
    ];

    const VALID_PROTOCOLS = [
        self::HTTP_PROTOCOL_NORMAL,
        self::HTTP_PROTOCOL_SECURE
    ];

    const STANDARD_PROTOCOL_PORTS = [
        self::HTTP_PROTOCOL_NORMAL      => 80,
        self::HTTP_PROTOCOL_SECURE      => 443
    ];

    const URL_GENERIC_DELIMETERS_REGEX = ':\/\?#\[\]@';
    
    const URL_SUB_DELIMETERS_REGEX = '\!\$&\'\(\)\*\+,;=';
    
    const URL_RESERVED_CHARS_REGEX = self::URL_GENERIC_DELIMETERS_REGEX . self::URL_SUB_DELIMETERS_REGEX;

    const URL_UNRESERVED_CHARS_REGEX = 'a-zA-Z0-9_\-\.~';

    const URL_PARTS_REGEX = '^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?';

    const RESPONSE_MEANINGS = [
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

        500 => 'Internal Server Error',
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

    public static function isValidMethod(string $method) : bool {
        return in_array(strtolower($method), static::VALID_METHODS);
    }

    public static function isValidProtocol(string $protocol) : bool {
        return in_array(strtolower($protocol), static::VALID_PROTOCOLS);
    }

    public static function upperCaseHexChars($component)
    {
        return \preg_replace_callback(
            '/%([A-Fa-f0-9]{2})/',
            function ($matches) {
                return \strtoupper($matches[0]);
            },
            $component
        );
    }

    public static function encodePath(string $path) : string
    {
        return \preg_replace_callback(
            '/(?:[^' . self::URL_UNRESERVED_CHARS_REGEX . self::URL_SUB_DELIMETERS_REGEX . ':@%\/]++|%(?![A-Fa-f0-9]{2}))/',
            function ($matches) {
                return self::upperCaseHexChars(\rawurlencode($matches[0]));
            },
            $path
        );
    }

    public static function encodeQueryOrFragment(string $component) : string
    {
        return \preg_replace_callback(
            '/(?:[^' . self::URL_UNRESERVED_CHARS_REGEX . self::URL_SUB_DELIMETERS_REGEX . ':@%\/\?]++|%(?![A-Fa-f0-9]{2}))/',
            function ($matches) {
                return self::upperCaseHexChars(\rawurlencode($matches[0]));
            },
            $component
        );
    }

    /**
     * Create a URI string from its various parts.
     */
    public static function createUriString(This\Data\Uri $uri) : string
    {
        $pieces = [];
        if ($uri->scheme() !== '') {
            array_push($pieces, $uri->scheme(), ':');
        }

        $authority = $uri->getAuthority();
        $path = $uri->getPath();
        $trimmedPath = \ltrim($path, '/');
        if ($authority !== '') {
            array_push($pieces, '//', $authority);
        }

        if ($path !== '') {
            if ($path === $trimmedPath) {
                if ($authority !== '') {
                    // If the path is rootless and an authority is present, the path MUST be prefixed by "/"
                    array_push($pieces, '/');
                }
            } else {
                if ($authority === '') {
                    // If the path is starting with more than one "/" and no authority is present, the
                    // starting slashes MUST be reduced to one.
                    $path = $trimmedPath;
                    array_push($pieces, '/');
                }
            }
            array_push($pieces, $path);
        }

        if ($uri->query() !== '') {
            array_push($pieces, '?', $uri->query());
        }

        if ($uri->fragment() !== '') {
            array_push($pieces, '#', $uri->fragment());
        }

        return implode($pieces);
    }
}
