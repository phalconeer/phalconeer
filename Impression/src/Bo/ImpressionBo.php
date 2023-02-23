<?php
namespace Phalconeer\Impression\Bo;

use Phalcon\Http as PhalconHttp;
use Phalconeer\Dao;
use Phalconeer\Http;
use Phalconeer\Impression as This;

class ImpressionBo
{
    protected string $impressionClass = This\Data\Impression::class;

    public function __construct(
        protected Dao\DaoReadAndWriteInterface $dao,
        protected PhalconHttp\Request $request,
        protected ?This\ImpressionInterface $impression = null,
    )
    {
        if (is_null($this->impression)) {
            $this->impression = new This\Data\Impression();
        }
        $this->impressionClass = get_class($this->impression);
        $this->initImpression();
    }

    public function initImpression()
    {
        $this->impression = $this->impression->merge(
            new $this->impressionClass(new \ArrayObject([
                'accept'        => $this->request->getServer('HTTP_ACCEPT') . $this->request->getServer('HTTP_ACCEPT_CHARSET'),
                'header'        => $this->request->getHeaders(),
                'host'          => $this->request->getHttpHost(),
                'ip'            => $this->request->getClientAddress(false),
                'language'      => $this->request->getServer('HTTP_ACCEPT_LANGUAGE'),
                'method'        => $this->request->getMethod(),
                'query'         => $this->request->getServer('REQUEST_URI'),
                'referer'       => $this->request->getHTTPReferer(),
                'requestTime'   => new \DateTime(),
                'server'        => $this->request->getServer('SERVER_ADDR'),
                'useragent'     => $this->request->getUserAgent(),
                'xForwrded'     => $this->request->getServer('HTTP_X_FORWARDED_FOR'),
            ]))
        );
    }

    public function setBody($body = null)
    {
        if (is_null($body)) {
            $body = json_decode($this->request->getRawBody(), true);
        }
        if (is_null($body)) {
            $body = [Http\Helper\MessageHelper::FULL_TEXT_BODY_ELASTIC => $this->request->getRawBody()];
        }
        $this->impression = $this->impression->setBody($body);
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
}