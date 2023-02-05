<?php
namespace Phalconeer\CurlClient\Bo;

use CurlHandle;
use Phalconeer\CurlClient as This;
use Phalconeer\Http;
use Psr;

class CurlClientBo implements This\CurlClientInterface
{
    protected ?This\Data\CurlOptions $options;

    protected ?This\Data\CurlOptions $baseOptions;

    const LARGE_BODY_LIMIT = 1024 * 1024;

    public function __construct(This\Data\CurlOptions $options = null)
    {
        if (is_null($options)) {
            $options = new This\Data\CurlOptions();
        }
        $this->baseOptions = $options;
        $this->options = $options;
    }

    public function withOption($key, $value) : void
    {
        $this->options = $this->options->setValueBykey($key, $value);
    }

    public function resetOptions() : void
    {
        $this->options = $this->baseOptions;
    }

    protected function setOption($curl, $option, $value) : self
    {
        curl_setopt($curl, $option, $value);
        return $this;
    }

    /**
     * Prepares a cURL resource to send a request.
     */
    protected function prepare(
        \CurlHandle $curl,
        Psr\Http\Message\RequestInterface $request,
        This\Data\CurlResponse $response = null
    ) : \CurlHandle
    {
        if (is_null($response)) {
            $response = new This\Data\CurlResponse();
        }

        $this->setClientOptions($curl);
        $this->setOptionsFromRequest($curl, $request);
        $this->setOption($curl, CURLOPT_HEADERFUNCTION, $response->readHeaderStream());
        $this->setOption($curl, CURLOPT_WRITEFUNCTION, $response->readBodyStream());

        return $curl;
    }

    /**
     * Transforms the stored protcol version to curl constant
     *
     * @return integer
     */
    protected function getCurlProtocolVersion(string $version) : int
    {
        switch ($version) {
            case Http\Helper\HttpHelper::HTTP_PROTOCOL_VERSION_1_0:
                return CURL_HTTP_VERSION_1_0;
            case Http\Helper\HttpHelper::HTTP_PROTOCOL_VERSION_1_1:
                return CURL_HTTP_VERSION_1_1;
            case Http\Helper\HttpHelper::HTTP_PROTOCOL_VERSION_2_0:
                if (\defined('CURL_HTTP_VERSION_2_0')) {
                    return CURL_HTTP_VERSION_2_0;
                }

                throw new \UnexpectedValueException('libcurl 7.33 needed for HTTP 2.0 support');
            default:
                return 0;
        }
    }

    /**
     * Sets options on a cURL resource based on a request.
     *
     * @param resource         $curl    A cURL resource
     * @param Psr\Http\Message\RequestInterface $request A request object
     */
    private function setOptionsFromRequest($curl, Psr\Http\Message\RequestInterface $request): void
    {
        $this->setOption($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod())
            ->setOption($curl, CURLOPT_URL, $request->getUri()->__toString())
            ->setOption($curl, CURLOPT_HTTPHEADER, $request->getHeaders())
            ->setOption($curl, CURLOPT_HTTP_VERSION, $this->getCurlProtocolVersion($request->getProtocolVersion()));

        if ($request->getUri()->getUserInfo()) {
            $this->setOption($curl, CURLOPT_USERPWD, $request->getUri()->getUserInfo());
        }

        switch ($request->getMethod()) {
            case Http\Helper\HttpHelper::HTTP_METHOD_HEAD:
                $this->setOption($curl, CURLOPT_NOBODY, true);
                break;

            case Http\Helper\HttpHelper::HTTP_METHOD_GET:
                $this->setOption($curl, CURLOPT_HTTPGET, true);
                break;

            case Http\Helper\HttpHelper::HTTP_METHOD_POST:
            case Http\Helper\HttpHelper::HTTP_METHOD_PUT:
            case Http\Helper\HttpHelper::HTTP_METHOD_DELETE:
            case Http\Helper\HttpHelper::HTTP_METHOD_PATCH:
            case Http\Helper\HttpHelper::HTTP_METHOD_OPTIONS:
                $body = $request->getBody();
                $bodySize = $body->getSize();
                if ($bodySize !== 0) {
                    if ($body->isSeekable()) {
                        $body->rewind();
                    }

                    // Message has non empty body.
                    if (is_null($bodySize) || $bodySize > self::LARGE_BODY_LIMIT) {
                        // Avoid full loading large or unknown size body into memory
                        $this->setOption($curl, CURLOPT_UPLOAD, true);
                        if (!is_null($bodySize)) {
                            $this->setOption($curl, CURLOPT_INFILESIZE, $bodySize);
                        }
                        $this->setOption($curl, CURLOPT_READFUNCTION, function ($ch, $fd, $length) use ($body) {
                            return $body->read($length);
                        });
                    } else {
                        // Small body can be loaded into memory
                        $this->setOption($curl, CURLOPT_POSTFIELDS, (string) $body);
                    }
                }
        }
    }

    /**
     */
    private function setClientOptions($curl): void
    {
        $this->options->configureHeader($curl)
                ->configureReturnTransfer($curl)
                ->configureFailOnError($curl)
                ->configureProxy($curl)
                ->configureProtocols($curl)
                ->configureRedirProtocols($curl)
                ->configureFollowLocation($curl)
                ->configureMaxRedirects($curl)
                ->configureVerifyPeer($curl)
                ->configureVerifyHost($curl)
                ->configureTimeout($curl)
                ->configureCurlInfoHeaderOut($curl)
                ->configureAuth($curl);
    }

    protected function parseError(Psr\Http\Message\RequestInterface $request, int $errno, \CurlHandle $curl): void
    {
        //TODO: proper error handling
        switch ($errno) {
            case CURLE_OK:
                // All OK, create a response object
                break;
            case CURLE_COULDNT_RESOLVE_PROXY:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_COULDNT_CONNECT:
            case CURLE_OPERATION_TIMEOUTED:
            case CURLE_SSL_CONNECT_ERROR:
                throw new This\Exception\NetworkException(curl_error($curl), This\Helper\ExceptionHelper::CURL_CLIENT_CALL__NETWORK_ERROR);
            case CURLE_ABORTED_BY_CALLBACK:
                throw new This\Exception\CallbackException(curl_error($curl), This\Helper\ExceptionHelper::CURL_CLIENT_CALL__CALLBACK_ERROR);
            default:
                throw new This\Exception\RequestException(curl_error($curl), This\Helper\ExceptionHelper::CURL_CLIENT_CALL__REQUEST_ERROR);
        }
    }

    public function sendRequest(
        Psr\Http\Message\RequestInterface $request
    ) : Psr\Http\Message\ResponseInterface
    {
        $response = new This\Data\CurlResponse();
        $curl = $this->prepare(curl_init(), $request, $response);
        $curlInfo = null;
        try {
            curl_exec($curl);
            $this->parseError($request, curl_errno($curl), $curl);
            if ($this->options->exposeCurlInfo()) {
                $curlInfo = curl_getinfo($curl);
            }
        } finally {
            curl_close($curl);
        }

        if (!is_null($curlInfo) && $value = json_encode($curlInfo)) {
            $response = $response->withHeader('__curl_info', $value);
        }
        return $response;
    }
}