<?php
namespace Phalconeer\Impression\Bo;

use Phalcon\Http as PhalconHttp;
use Phalconeer\Application;
use Phalconeer\Dao;
use Phalconeer\Http;
use Phalconeer\Id;
use Phalconeer\Impression as This;

class ImpressionBo
{
    protected string $impressionClass = This\Data\Impression::class;

    protected string $requestId;

    public function __construct(
        protected Dao\DaoReadAndWriteInterface $dao,
        protected PhalconHttp\Request $request,
        protected Application\ApplicationInterface $application,
        protected This\ImpressionInterface $impression = new This\Data\Impression(),
    )
    {
        $this->impressionClass = get_class($this->impression);
        $this->generateRequestId();
    }

    protected function generateRequestId() : void
    {
        $this->requestId = Id\Helper\IdHelper::generateWithDayPrefix(12);
    }

    public function initImpression($body = null)
    {
        if (is_null($body)) {
            $body = json_decode($this->request->getRawBody(), true);
        }
        if (is_null($body)) {
            $body = [Http\Helper\MessageHelper::FULL_TEXT_BODY_ELASTIC => $this->request->getRawBody()];
        }
        $this->impression = new $this->impressionClass([
            'host'          => $this->request->getHttpHost(),
            'method'        => $this->request->getMethod(),
            'query'         => $this->request->getServer('REQUEST_URI'),
            'header'        => $this->request->getHeaders(),
            'body'          => $body,
            'referer'       => $this->request->getHTTPReferer(),
            'requestTime'   => new \DateTime(),
            'requestId'     => $this->requestId,
            'ip'            => $this->request->getClientAddress(false),
            'xForwrded'     => $this->request->getServer('HTTP_X_FORWARDED_FOR'),
            'country'       => $this->request->getServer('HTTP_X_COUNTRY'),
            // 'region'        => '',
            // 'city'          => '',
            'useragent'     => $this->request->getUserAgent(),
            'session'       => '?',
            'language'      => $this->request->getServer('HTTP_ACCEPT_LANGUAGE'),
            'accept'        => $this->request->getServer('HTTP_ACCEPT') . $this->request->getServer('HTTP_ACCEPT_CHARSET'),
            'server'        => $this->request->getServer('SERVER_ADDR'),
            'application'   => $this->application->getName()
        ]);
    }

    public function save()
    {
        return $this->dao->save($this->impression);
    }

    public function addTag(string $tag)
    {
        $this->impression = $this->impression->addTag($tag);
    }

    public function getImpressionCount(array $whereConditions)
    {
        return $this->dao->getCount($whereConditions);
    }

    public function getRequestId() : ?string
    {
        return $this->requestId;
    }
}