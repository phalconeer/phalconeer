<?php
namespace Phalconeer\Impression\Bo;

use Phalconeer\Dao;
use Phalconeer\Http;
use Phalconeer\Impression as This;
use Phalcon;

class ImpressionBo implements This\ImpressionBoInterface
{
    protected This\ImpressionInterface $impression;

    protected string $impressionClass;

    protected \ArrayObject $adapters;

    public function __construct(
        protected Phalcon\Http\Request $request,
        protected Phalcon\Config\Config $config,
    )
    {
        $this->impressionClass = $this->config->get('impressionClass', This\Data\Impression::class);
        $this->impression = new $this->impressionClass(new \ArrayObject([
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
        ]));

        $this->setBody();

        $this->adapters = new \ArrayObject();
    }

    public function mergeData(array $data = [])
    {
        $this->impression = $this->impression->merge(
            new $this->impressionClass(new \ArrayObject($data))
        );
    }

    public function setBody($body = null)
    {
        if (is_null($body)) {
            $body = json_decode($this->request->getRawBody(), true);
        }
        $this->impression = $this->impression->setBody($body ?? $this->request->getRawBody());
    }

    public function addAdapter(Dao\DaoReadAndWriteInterface $adapter)
    {
        $this->adapters->offsetSet(null, $adapter);
    }

    public function save()
    {
        $iterator = $this->adapters->getIterator();
        while ($iterator->valid()) {
            $iterator->current()->save($this->impression);
            $iterator->next();
        }
    }

    public function addTag(string $tag)
    {
        $this->impression = $this->impression->addTag($tag);
    }

    public function impression() : This\ImpressionInterface
    {
        return clone($this->impression);
    }
}