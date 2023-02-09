<?php
namespace Phalconeer\Http\Test;

use ArrayObject;
use Test;
use Phalconeer\Http;

/**
 * Description of AccountingAccountBoTest
 *
 * @author Fulee
 */
class UriTest extends Test\UnitTestCase
{
    /**
     * Retrieve the scheme component of the URI.
     *
     * If no scheme is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.1.
     *
     * The trailing ":" character is not part of the scheme and MUST NOT be
     * added.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function testGetScheme()
    {
        $uri = new Http\Data\Uri();
        $uri2 = new Http\Data\Uri(new ArrayObject([
            'scheme'    => null
        ]));
        $this->assertEquals('', $uri->getScheme(), 'Unset port returns empty string');
        $this->assertEquals('', $uri2->getScheme(), 'Unset port returns empty string');

        $uri = new Http\Data\Uri(new ArrayObject([
            'scheme'        => Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL
        ]));
        $this->assertEquals(Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL, $uri->getScheme(), 'Constructor parameter works');
        $uri2 = $uri->withScheme(Http\Helper\HttpHelper::HTTP_PROTOCOL_SECURE);
        $this->assertEquals(Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL, $uri->getScheme(), 'Uri object is immutable');
        $this->assertEquals(Http\Helper\HttpHelper::HTTP_PROTOCOL_SECURE, $uri2->getScheme(), 'New object contains correct value');
        
        $uri = new Http\Data\Uri(new ArrayObject([
            'scheme'        => strtoupper(Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL)
        ]));
        $this->assertEquals(Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL, $uri->getScheme(), 'Scheme is always returned as lowercase');

        $uri = new Http\Data\Uri(new ArrayObject([
            'scheme'        => Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL,
            'port'          => 8088
        ]));
        $this->assertEquals(Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL, $uri->getScheme(), 'Constructor parameter works');
        $this->assertEquals(8088, $uri->getPort(), 'Constructor parameter works for port');
        $uri2 = $uri->withScheme('file');
        $this->assertEquals('file', $uri2->getScheme(), 'Scheme is updated');
        $this->assertEquals(8088, $uri2->getPort(), 'Port is preserved on non-standard ports');
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * If no authority information is present, this method MUST return an empty
     * string.
     *
     * The authority syntax of the URI is:
     *
     * <pre>
     * [user-info@]host[:port]
     * </pre>
     *
     * If the port component is not set or is the standard port for the current
     * scheme, it SHOULD NOT be included.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function testGetAuthority()
    {
        $uri = new Http\Data\Uri();
        $uri2 = new Http\Data\Uri(new ArrayObject([
            'host'      => null
        ]));
        $this->assertEquals('', $uri->getAuthority(), 'Unset authority returns empty string');
        $this->assertEquals('', $uri2->getAuthority(), 'Unset authority returns empty string');

        $uri = new Http\Data\Uri(new ArrayObject([
            'user'      => 'test',
            'pass'      => 'pass123'
        ]));
        $this->assertEquals('', $uri->getAuthority(), 'Unset host results in no authority');

        $uri = new Http\Data\Uri(new ArrayObject([
            'user'      => 'test',
            'host'      => 'a.com'
        ]));
        $uri2 = (new Http\Data\Uri())->withUserInfo('test')
                    ->withHost('a.com');
        $this->assertEquals('test@a.com', $uri->getAuthority(), 'Only username resolves correctly');
        $this->assertEquals('test@a.com', $uri2->getAuthority(), 'Only username resolves correctly');

        $uri = new Http\Data\Uri(new ArrayObject([
            'user'      => 'test',
            'pass'      => 'pass123',
            'host'      => 'a.com'
        ]));
        $uri2 = (new Http\Data\Uri())->withUserInfo('test', 'pass123')
                    ->withHost('a.com');
        $this->assertEquals('test:pass123@a.com', $uri->getAuthority(), 'User and pass resolved correclty');
        $this->assertEquals('test:pass123@a.com', $uri2->getAuthority(), 'User and pass resolved correclty');

        $uri = new Http\Data\Uri(new ArrayObject([
            'user'      => 'test',
            'pass'      => 'pass123',
            'host'      => 'a.com',
            'port'      => 1111
        ]));
        $uri2 = (new Http\Data\Uri())->withUserInfo('test', 'pass123')
                    ->withHost('a.com')
                    ->withPort(1111);
        $this->assertEquals('test:pass123@a.com:1111', $uri->getAuthority(), 'All parts of authority resolved correctly');
        $this->assertEquals('test:pass123@a.com:1111', $uri2->getAuthority(), 'All parts of authority resolved correctly');

        $uri = (new Http\Data\Uri())->withUri('//a.com')
                ->withPort(80);

        $this->assertEquals('a.com:80', $uri->getAuthority());
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * If no user information is present, this method MUST return an empty
     * string.
     *
     * If a user is present in the URI, this will return that value;
     * additionally, if the password is also present, it will be appended to the
     * user value, with a colon (":") separating the values.
     *
     * The trailing "@" character is not part of the user information and MUST
     * NOT be added.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function testGetUserInfo()
    {
        $uri = new Http\Data\Uri();
        $uri2 = new Http\Data\Uri(new ArrayObject([
            'user'      => null
        ]));
        $this->assertEquals('', $uri->getUserInfo(), 'Unset user info returns empty string');
        $this->assertEquals('', $uri2->getUserInfo(), 'Unset user info returns empty string');

        $uri = new Http\Data\Uri(new ArrayObject([
            'user'      => 'test'
        ]));
        $uri2 = (new Http\Data\Uri())->withUserInfo('test');
        $this->assertEquals('test', $uri->getUserInfo(), 'Userinfo is returned correctly with only user');
        $this->assertEquals('test', $uri2->getUserInfo(), 'Userinfo is returned correctly with only user');

        $uri = new Http\Data\Uri(new ArrayObject([
            'user'      => 'test',
            'pass'      => 'pass123'
        ]));
        $uri2 = (new Http\Data\Uri())->withUserInfo('test', 'pass123');
        $this->assertEquals('test:pass123', $uri->getUserInfo(), 'Userinfo is returned correctly');
        $this->assertEquals('test:pass123', $uri2->getUserInfo(), 'Userinfo is returned correctly');
    }

    /**
     * Retrieve the host component of the URI.
     *
     * If no host is present, this method MUST return an empty string.
     *
     * The value returned MUST be normalized to lowercase, per RFC 3986
     * Section 3.2.2.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function testGetHost()
    {
        $uri = new Http\Data\Uri();
        $uri2 = new Http\Data\Uri(new ArrayObject([
            'host'      => null
        ]));
        $this->assertEquals('', $uri->getHost(), 'Unset host returns empty string');
        $this->assertEquals('', $uri2->getHost(), 'Unset host returns empty string');

        $uri = new Http\Data\Uri(new ArrayObject([
            'host'      => 'a.com'
        ]));
        $uri2 = (new Http\Data\Uri())->withHost('a.com');
        $this->assertEquals('a.com', $uri->getHost(), 'Host is returned properly');
        $this->assertEquals('a.com', $uri2->getHost(), 'Host is returned properly');

        $uri = new Http\Data\Uri(new ArrayObject([
            'host'      => 'A.COM'
        ]));
        $uri2 = (new Http\Data\Uri())->withHost('A.COM');
        $this->assertEquals('a.com', $uri->getHost(), 'Host is returned lowercase');
        $this->assertEquals('a.com', $uri2->getHost(), 'Host is returned lowercase');
    }

    /**
     * Retrieve the port component of the URI.
     *
     * If a port is present, and it is non-standard for the current scheme,
     * this method MUST return it as an integer. If the port is the standard port
     * used with the current scheme, this method SHOULD return null.
     *
     * If no port is present, and no scheme is present, this method MUST return
     * a null value.
     *
     * If no port is present, but a scheme is present, this method MAY return
     * the standard port for that scheme, but SHOULD return null.
     *
     * @return null|int The URI port.
     */
    public function testGetPort()
    {
        $uri = new Http\Data\Uri();
        $this->assertEquals(null, $uri->getPort(), 'Unset port returns null');

        $uri = new Http\Data\Uri(new ArrayObject([
            'port'      => 1111
        ]));
        $uri2 = (new Http\Data\Uri())->withPort(1111);
        $this->assertEquals(1111, $uri->getPort(), 'Unset protocol keeps custom port');
        $this->assertEquals(1111, $uri2->getPort(), 'Unset protocol keeps custom port');

        $uri = new Http\Data\Uri(new ArrayObject([
            'scheme'    => Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL,
            'port'      => 80
        ]));
        $uri2 = (new Http\Data\Uri())->withScheme(Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL)
                ->withPort(80);
        $uri3 = (new Http\Data\Uri())->withPort(80)
                ->withScheme(Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL);
        $this->assertEquals(null, $uri->getPort(), 'Standard protocol with standard port returns null');
        $this->assertEquals(null, $uri2->getPort(), 'Standard protocol with standard port returns null');
        $this->assertEquals(null, $uri3->getPort(), 'Standard protocol with standard port returns null');

        $uri = new Http\Data\Uri(new ArrayObject([
            'scheme'    => Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL,
            'port'      => 1111
        ]));
        $uri2 = (new Http\Data\Uri())->withScheme(Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL)
                ->withPort(1111);
        $uri3 = (new Http\Data\Uri())->withPort(1111)
                ->withScheme(Http\Helper\HttpHelper::HTTP_PROTOCOL_NORMAL);
        $this->assertEquals(1111, $uri->getPort(), 'Standard protocol with custom port keeps custom port');
        $this->assertEquals(1111, $uri2->getPort(), 'Standard protocol with custom port keeps custom port');
        $this->assertEquals(1111, $uri3->getPort(), 'Standard protocol with custom port keeps custom port');

        $uri = new Http\Data\Uri(new ArrayObject([
            'scheme'    => 'file',
            'port'      => 1111
        ]));
        $uri2 = (new Http\Data\Uri())->withScheme('file')
                ->withPort(1111);
        $uri3 = (new Http\Data\Uri())->withPort(1111)
                ->withScheme('file');
        $this->assertEquals(1111, $uri->getPort(), 'Non Standard protocol keeps custom port');
        $this->assertEquals(1111, $uri2->getPort(), 'Non Standard protocol keeps custom port');
        $this->assertEquals(1111, $uri3->getPort(), 'Non Standard protocol keeps custom port');

        $uri = new Http\Data\Uri(new ArrayObject([
            'port'      => '8080'
        ]));

        $this->assertEquals(8080, $uri->getPort(), 'Port is returned as integer');

        $uri = (new Http\Data\Uri())->withUri('http://example.com:8080')
                ->withPort(null);

        $this->assertNull($uri->getPort());

    }

    /**
     * Retrieve the path component of the URI.
     *
     * The path can either be empty or absolute (starting with a slash) or
     * rootless (not starting with a slash). Implementations MUST support all
     * three syntaxes.
     *
     * Normally, the empty path "" and absolute path "/" are considered equal as
     * defined in RFC 7230 Section 2.7.3. But this method MUST NOT automatically
     * do this normalization because in contexts with a trimmed base path, e.g.
     * the front controller, this difference becomes significant. It's the task
     * of the user to handle both "" and "/".
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.3.
     *
     * As an example, if the value should include a slash ("/") not intended as
     * delimiter between path segments, that value MUST be passed in encoded
     * form (e.g., "%2F") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function testGetPath()
    {
        $uri = new Http\Data\Uri();
        $uri2 = new Http\Data\Uri(new ArrayObject([
            'path'      => null
        ]));
        $this->assertEquals('', $uri->getPath(), 'Unset path returns empty string');
        $this->assertEquals('', $uri2->getPath(), 'Unset path returns empty string');

        $uri = new Http\Data\Uri(new ArrayObject([
            'path'      => '/this/is/a/valid/path'
        ]));
        $uri2 = (new Http\Data\Uri())->withPath('/this/is/a/valid/path');
        $this->assertEquals('/this/is/a/valid/path', $uri->getPath(), 'Path returns correctly using only reserved characters');
        $this->assertEquals('/this/is/a/valid/path', $uri2->getPath(), 'Path returns correctly using only reserved characters');

        $uri = new Http\Data\Uri(new ArrayObject([
            'path'      => '/valid but unsafe'
        ]));
        $uri2 = (new Http\Data\Uri())->withPath('/valid but unsafe');
        $this->assertEquals('/valid%20but%20unsafe', $uri->getPath(), 'Path returns encoded');
        $this->assertEquals('/valid%20but%20unsafe', $uri2->getPath(), 'Path returns encoded');

        $uri = new Http\Data\Uri(new ArrayObject([
            'path'      => '/avoid%20double enconding'
        ]));
        $uri2 = (new Http\Data\Uri())->withPath('/avoid%20double enconding');
        $this->assertEquals('/avoid%20double%20enconding', $uri->getPath(), 'Path returns encoded');
        $this->assertEquals('/avoid%20double%20enconding', $uri2->getPath(), 'Path returns encoded');

        $uri = new Http\Data\Uri(new ArrayObject([
            'path'      => 'test@a.com'
        ]));
        $uri2 = (new Http\Data\Uri())->withPath('test@a.com');
        $this->assertEquals('test@a.com', $uri->getPath(), 'Path returns encoded');
        $this->assertEquals('test@a.com', $uri2->getPath(), 'Path returns encoded');

        $uri = new Http\Data\Uri(new ArrayObject([
            'path'      => '/baz?#€/b%61r'
        ]));
        // Query and fragment delimiters and multibyte chars are encoded.
        $this->assertSame('/baz%3F%23%E2%82%AC/b%61r', $uri->getPath());
        $this->assertSame('/baz%3F%23%E2%82%AC/b%61r', $uri->__toString());
    }

    /**
     * Retrieve the query string of the URI.
     *
     * If no query string is present, this method MUST return an empty string.
     *
     * The leading "?" character is not part of the query and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.4.
     *
     * As an example, if a value in a key/value pair of the query string should
     * include an ampersand ("&") not intended as a delimiter between values,
     * that value MUST be passed in encoded form (e.g., "%26") to the instance.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function testGetQuery()
    {
        $uri = new Http\Data\Uri();
        $uri2 = new Http\Data\Uri(new ArrayObject([
            'query'     => null
        ]));
        $this->assertEquals('', $uri->getQuery(), 'Unset query returns empty string');
        $this->assertEquals('', $uri2->getQuery(), 'Unset query returns empty string');

        $uri = new Http\Data\Uri(new ArrayObject([
            'query'         => 'key=value&path=this/is/valid?'
        ]));
        $uri2 = (new Http\Data\Uri())->withQuery('key=value&path=this/is/valid?');
        $this->assertEquals('key=value&path=this/is/valid?', $uri->getQuery(), 'Query returns correctly using only reserved characters');
        $this->assertEquals('key=value&path=this/is/valid?', $uri2->getQuery(), 'Query returns correctly using only reserved characters');

        $uri = new Http\Data\Uri(new ArrayObject([
            'query'         => 'encode=should work fine'
        ]));
        $uri2 = (new Http\Data\Uri())->withQuery('encode=should work fine');
        $this->assertEquals('encode=should%20work%20fine', $uri->getQuery(), 'Query returns correctly using only reserved characters');
        $this->assertEquals('encode=should%20work%20fine', $uri2->getQuery(), 'Query returns correctly using only reserved characters');

        $uri = new Http\Data\Uri(new ArrayObject([
            'query'         => 'avoid=double%20enconding any input'
        ]));
        $uri2 = (new Http\Data\Uri())->withQuery('avoid=double%20enconding any input');
        $this->assertEquals('avoid=double%20enconding%20any%20input', $uri->getQuery(), 'Query returns correctly using only reserved characters');
        $this->assertEquals('avoid=double%20enconding%20any%20input', $uri2->getQuery(), 'Query returns correctly using only reserved characters');

        $uri = new Http\Data\Uri(new ArrayObject([
            'query'     => '?=#&€=/&b%61r'
        ]));
        // A query starting with a "?" is valid and must not be magically removed. Otherwise it would be impossible to
        // construct such an URI. Also the "?" and "/" does not need to be encoded in the query.
        $this->assertEquals('?=%23&%E2%82%AC=/&b%61r', $uri->getQuery());
        $this->assertEquals('??=%23&%E2%82%AC=/&b%61r', $uri->__toString());

    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * If no fragment is present, this method MUST return an empty string.
     *
     * The leading "#" character is not part of the fragment and MUST NOT be
     * added.
     *
     * The value returned MUST be percent-encoded, but MUST NOT double-encode
     * any characters. To determine what characters to encode, please refer to
     * RFC 3986, Sections 2 and 3.5.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function testGetFragment()
    {
        $uri = new Http\Data\Uri();
        $uri2 = new Http\Data\Uri(new ArrayObject([
            'fragment'     => null
        ]));
        $this->assertEquals('', $uri->getFragment(), 'Unset fragment returns empty string');
        $this->assertEquals('', $uri2->getFragment(), 'Unset fragment returns empty string');

        $uri = new Http\Data\Uri(new ArrayObject([
            'fragment'         => 'key=value&path=this/is/valid?'
        ]));
        $uri2 = (new Http\Data\Uri())->withFragment('key=value&path=this/is/valid?');
        $this->assertEquals('key=value&path=this/is/valid?', $uri->getFragment(), 'Fragment returns correctly using only reserved characters');
        $this->assertEquals('key=value&path=this/is/valid?', $uri2->getFragment(), 'Fragment returns correctly using only reserved characters');

        $uri = new Http\Data\Uri(new ArrayObject([
            'fragment'         => 'encode=should work fine'
        ]));
        $uri2 = (new Http\Data\Uri())->withFragment('encode=should work fine');
        $this->assertEquals('encode=should%20work%20fine', $uri->getFragment(), 'Fragment returns correctly using only reserved characters');
        $this->assertEquals('encode=should%20work%20fine', $uri2->getFragment(), 'Fragment returns correctly using only reserved characters');

        $uri = new Http\Data\Uri(new ArrayObject([
            'fragment'         => 'avoid=double%20enconding any input'
        ]));
        $uri2 = (new Http\Data\Uri())->withFragment('avoid=double%20enconding any input');
        $this->assertEquals('avoid=double%20enconding%20any%20input', $uri->getFragment(), 'Fragment returns correctly using only reserved characters');
        $this->assertEquals('avoid=double%20enconding%20any%20input', $uri2->getFragment(), 'Fragment returns correctly using only reserved characters');

        $uri = new Http\Data\Uri(new ArrayObject([
            'fragment'      => '#€?/b%61r'
        ]));
        // A fragment starting with a "#" is valid and must not be magically removed. Otherwise it would be impossible to
        // construct such an URI. Also the "?" and "/" does not need to be encoded in the fragment.
        $this->assertEquals('%23%E2%82%AC?/b%61r', $uri->getFragment());
        $this->assertEquals('#%23%E2%82%AC?/b%61r', (string) $uri);
    }


    /**
     * Return the string representation as a URI reference.
     *
     * Depending on which components of the URI are present, the resulting
     * string is either a full URI or relative reference according to RFC 3986,
     * Section 4.1. The method concatenates the various components of the URI,
     * using the appropriate delimiters:
     *
     * - If a scheme is present, it MUST be suffixed by ":".
     * - If an authority is present, it MUST be prefixed by "//".
     * - The path can be concatenated without delimiters. But there are two
     *   cases where the path has to be adjusted to make the URI reference
     *   valid as PHP does not allow to throw an exception in __toString():
     *     - If the path is rootless and an authority is present, the path MUST
     *       be prefixed by "/".
     *     - If the path is starting with more than one "/" and no authority is
     *       present, the starting slashes MUST be reduced to one.
     * - If a query is present, it MUST be prefixed by "?".
     * - If a fragment is present, it MUST be prefixed by "#".
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function testToString()
    {
        $uri = new Http\Data\Uri();
        $uri2 = (new Http\Data\Uri())->withUri();
        $this->assertEquals('', $uri->__toString(), 'Unset URL returns empty string');
        $this->assertEquals('', $uri2->__toString(), 'Unset URL returns empty string');

        $uri = new Http\Data\Uri(new ArrayObject([
            'scheme'        => Http\Helper\HttpHelper::HTTP_PROTOCOL_SECURE,
            'user'          => 'test',
            'pass'          => 'pass123',
            'host'          => 'a.com',
            'port'          => 8080,
            'path'          => '/some/valid/path',
            'query'         => 'key=value',
            'fragment'      => 'bookmark'
        ]));
        $uri2 = (new Http\Data\Uri())
            ->withScheme('https')
            ->withUserInfo('test', 'pass123')
            ->withHost('a.com')
            ->withPort(8080)
            ->withPath('/some/valid/path')
            ->withQuery('key=value')
            ->withFragment('bookmark');
        $uri3 = (new Http\Data\Uri())->withUri('https://test:pass123@a.com:8080/some/valid/path?key=value#bookmark');
        $this->assertEquals(
            'https://test:pass123@a.com:8080/some/valid/path?key=value#bookmark',
            $uri->__toString(),
            'String conversion of all items set is okay'
        );
        $this->assertEquals(
            'https://test:pass123@a.com:8080/some/valid/path?key=value#bookmark',
            $uri2->__toString(),
            'String conversion of all items set is okay'
        );
        $this->assertEquals(
            'https://test:pass123@a.com:8080/some/valid/path?key=value#bookmark',
            $uri3->__toString(),
            'String conversion of all items set is okay'
        );

        $uri = new Http\Data\Uri(new ArrayObject([
            'path'      => 'foo'
        ]));
        $this->assertEquals('foo', $uri->__toString());

        $uri = new Http\Data\Uri(new ArrayObject([
            'path'      => 'foo',
            'host'      => 'a.com'
        ]));
        $this->assertEquals('//a.com/foo', $uri->__toString());

        $uri = new Http\Data\Uri(new ArrayObject([
            'path'      => '//foo',
        ]));
        $this->assertEquals('/foo', $uri->__toString());
    }

    /**
     * @dataProvider getValidUris
     */
    public function testValidUrisStayValid($input)
    {
        $uri = (new Http\Data\Uri())->withUri($input);
        $this->assertEquals($input, $uri->__toString(), 'URI `' . $input . '` preserved');
    }

    public function getValidUris()
    {
        return [
            ['urn:path-rootless'],
            ['urn:path:with:colon'],
            ['urn:/path-absolute'],
            ['urn:/'],
            // only scheme with empty path
            ['urn:'],
            // only path
            ['/'],
            ['relative/'],
            ['0'],
            // same document reference
            [''],
            // network path without scheme
            ['//example.org'],
            ['//example.org/'],
            ['//example.org?q#h'],
            // only query
            ['?q'],
            ['?q=abc&foo=bar'],
            // only fragment
            ['#fragment'],
            // dot segments are not removed automatically
            ['./foo/../bar'],
        ];
    }

    public function uriComponentsEncodingProvider()
    {
        $unreserved = 'a-zA-Z0-9.-_~!$&\'()*+,;=:@';

        return [
            // Percent encode spaces
            ['/pa th?q=va lue#frag ment', '/pa%20th', 'q=va%20lue', 'frag%20ment', '/pa%20th?q=va%20lue#frag%20ment'],
            // Percent encode multibyte
            ['/€?€#€', '/%E2%82%AC', '%E2%82%AC', '%E2%82%AC', '/%E2%82%AC?%E2%82%AC#%E2%82%AC'],
            // Don't encode something that's already encoded
            ['/pa%20th?q=va%20lue#frag%20ment', '/pa%20th', 'q=va%20lue', 'frag%20ment', '/pa%20th?q=va%20lue#frag%20ment'],
            // Percent encode invalid percent encodings
            ['/pa%2-th?q=va%2-lue#frag%2-ment', '/pa%252-th', 'q=va%252-lue', 'frag%252-ment', '/pa%252-th?q=va%252-lue#frag%252-ment'],
            // Don't encode path segments
            ['/pa/th//two?q=va/lue#frag/ment', '/pa/th//two', 'q=va/lue', 'frag/ment', '/pa/th//two?q=va/lue#frag/ment'],
            // Don't encode unreserved chars or sub-delimiters
            ["/$unreserved?$unreserved#$unreserved", "/$unreserved", $unreserved, $unreserved, "/$unreserved?$unreserved#$unreserved"],
            // Encoded unreserved chars are not decoded
            ['/p%61th?q=v%61lue#fr%61gment', '/p%61th', 'q=v%61lue', 'fr%61gment', '/p%61th?q=v%61lue#fr%61gment'],
        ];
    }

    /**
     * @dataProvider uriComponentsEncodingProvider
     */
    public function testUriComponentsGetEncodedProperly($input, $path, $query, $fragment, $output)
    {
        $uri = (new Http\Data\Uri())->withUri($input);
        $this->assertSame($path, $uri->getPath());
        $this->assertSame($query, $uri->getQuery());
        $this->assertSame($fragment, $uri->getFragment());
        $this->assertSame($output, (string) $uri);
    }

    public function testCanParseFalseyUriParts()
    {
        $uri = (new Http\Data\Uri())->withUri('0://0:0@0/0?0#0');

        $this->assertSame('0', $uri->getScheme());
        $this->assertSame('0:0@0', $uri->getAuthority());
        $this->assertSame('0:0', $uri->getUserInfo());
        $this->assertSame('0', $uri->getHost());
        $this->assertSame('/0', $uri->getPath());
        $this->assertSame('0', $uri->getQuery());
        $this->assertSame('0', $uri->getFragment());
        $this->assertSame('0://0:0@0/0?0#0', (string) $uri);
    }

    public function testCanConstructFalseyUriParts()
    {
        $uri = (new Http\Data\Uri())
            ->withScheme('0')
            ->withUserInfo('0', '0')
            ->withHost('0')
            ->withPath('/0')
            ->withQuery('0')
            ->withFragment('0');

        $this->assertSame('0', $uri->getScheme());
        $this->assertSame('0:0@0', $uri->getAuthority());
        $this->assertSame('0:0', $uri->getUserInfo());
        $this->assertSame('0', $uri->getHost());
        $this->assertSame('/0', $uri->getPath());
        $this->assertSame('0', $uri->getQuery());
        $this->assertSame('0', $uri->getFragment());
        $this->assertSame('0://0:0@0/0?0#0', (string) $uri);
    }
}