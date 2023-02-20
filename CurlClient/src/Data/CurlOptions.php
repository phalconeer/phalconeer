<?php
namespace Phalconeer\CurlClient\Data;

use Phalconeer\Data;

class CurlOptions extends Data\ImmutableData
{
    use Data\Trait\Data\AutoGetter,
        Data\Trait\Data\ParseTypes;

    protected bool $allowRedirects = false;

    protected bool $curlInfoHeaderOut = true;

    protected bool $exposeCurlInfo = true;

    protected bool $failOnError = false;

    protected bool $header = false;

    protected string $httpAuth;

    protected int $httpVersion = CURL_HTTP_VERSION_1_1;

    protected int $maxRedirects = 5;

    protected int $protocols = CURLPROTO_HTTP | CURLPROTO_HTTPS;

    protected string $proxy;

    protected int $redirProtocols = CURLPROTO_HTTP | CURLPROTO_HTTPS;
 
    protected bool $returnTransfer = false;

    protected ?int $timeout;

    protected string $userPwd;

    /**
     * 0 - no name check
     * 1 - check the existence of a common name in the SSL peer certificate
     * 2 - check the existence of a common name and also verify that it matches the hostname provided
     * In production environments the value of this option should be kept at 2 (default value)
     */
    protected int $verifyHost = 2;

    protected bool $verifyPeer = true;

    public function configureHeader($curl) : self
    {
        curl_setopt($curl, CURLOPT_HEADER, $this->header());
        return $this;
    }

    public function configureReturnTransfer($curl) : self
    {
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, $this->returnTransfer());
        return $this;
    }

    public function configureFailOnError($curl) : self
    {
        curl_setopt($curl, CURLOPT_FAILONERROR, $this->failOnError());
        return $this;
    }

    protected function canFollow() : bool
    {
        return !ini_get('safe_mode')
                && !ini_get('open_basedir')
                && $this->allowRedirects();
    }

    public function configureFollowLocation($curl) : self
    {
        curl_setopt(
            $curl,
            CURLOPT_FOLLOWLOCATION,
            $this->canFollow()
        );

        return $this;
    }

    public function configureMaxRedirects($curl) : self
    {
        curl_setopt(
            $curl,
            CURLOPT_MAXREDIRS,
            ($this->canFollow())
                ? $this->maxRedirects()
                : 0
        );

        return $this;
    }

    public function configureTimeout($curl) : self
    {
        if (!is_null($this->timeout())) {
            curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout());
        }
        return $this;
    }

    public function configureVerifyPeer($curl) : self
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->verifyPeer());
        return $this;
    }

    public function configureVerifyHost($curl) : self
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $this->verifyHost());
        return $this;
    }

    public function configureProxy($curl) : self
    {
        if (!is_null($this->proxy())) {
            curl_setopt($curl, CURLOPT_PROXY, $this->proxy());
        }
        return $this;
    }

    public function configureProtocols($curl) : self
    {
        curl_setopt($curl, CURLOPT_PROTOCOLS, $this->protocols());
        return $this;
    }

    public function configureRedirProtocols($curl) : self
    {
        curl_setopt($curl, CURLOPT_REDIR_PROTOCOLS, $this->redirProtocols());
        return $this;
    }

    public function configureCurlInfoHeaderOut($curl) : self
    {
        curl_setopt($curl, CURLINFO_HEADER_OUT, $this->curlInfoHeaderOut());
        return $this;
    }

    public function configureAuth($curl) : self
    {
        switch ($this->httpAuth) {
            case CURLAUTH_BASIC:
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($curl, CURLOPT_USERPWD, $this->userPwd);
                break;
        }
        return $this;
    }

    public function setHttpAuth(string $httpAuth) : self
    {
        switch ($httpAuth) {
            case CURLAUTH_BASIC:
                return $this->setKeyValue('httpAuth', $httpAuth);
        }

        return $this;
    }

    public function setUserPwd(string $userPwd) : self
    {
        return $this->setKeyValue('userPwd', $userPwd);
    }
}