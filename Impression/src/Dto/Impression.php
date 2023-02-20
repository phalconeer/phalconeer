<?php
namespace Phalconeer\Module\Impression\Dto;

use ArrayObject;
use Phalconeer\Module\Dto\DtoPrototypingTrait;
use Phalconeer\Module\Dto\ParseTypesTrait;
use Phalconeer\Module\Dto\Transformer\DateTransformerElasticTrait;
use Phalconeer\Module\ElasticAdapter\Dto\ElasticBase;

class Impression extends ElasticBase
{
    use DateTransformerElasticTrait, ParseTypesTrait, DtoPrototypingTrait;

    /**
     * @var array
     */
    protected static $_keyAliases = [
        'atTimestamp'   => '@timestamp',
        'atVersion'     => '@version',
    ];

    protected $_indexDateField = 'requestTime';
    
    /**
     * Accepted MIME types of the visitor.
     * @var string 
     */
    protected $accept;
    
    /**
     * @var string 
     */
    protected $application;
    
    /**
     * Accepted MIME types of the visitor.
     * @var array 
     */
    protected $body;
    
    /**
     * Country, based on visitor's IP.
     * @var string 
     */
    protected $country;

    
    /**
     * @var array 
     */
    protected $header;

    /**
     * Where the record were taken.
     * @var string 
     */
    protected $host;
    
    /**
     * IP of the visitor.
     * @var array 
     */
    protected $ip;
    
    /**
     * Language settings of the visitor's browser.
     * @var string 
     */
    protected $language;
    
    /**
     * @var string 
     */
    protected $method;
    
    /**
     * Detailed URI of the request.
     * @var string 
     */
    protected $query;
    
    /**
     * Referer of the request-
     * @var string 
     */
    protected $referer;
    
    /**
     * @var string 
     */
    protected $requestId;
    
    /**
     * Time of the request in Elastic domestic format.
     * @var \DateTime
     */
    protected $requestTime;

    /**
     * @var string 
     */
    protected $server;

    /**
     * Session ID.
     * @var string 
     */
    protected $session;

    /**
     * Session ID.
     * @var \ArrayObject 
     */
    protected $tags;

    /**
     * User agent of visitor.
     * @var string 
     */
    protected $useragent;

    /**
     * IP of the visitor.
     * @var string 
     */
    protected $xForwarded;

    /**
     * Any additional data formatting required for the obejct can be included here.
     *
     * @param array $input
     * @return array
     */
    protected function convertData(ArrayObject $inputObject) : ArrayObject 
    {
        $inputObject = parent::convertData($inputObject);
        if ($inputObject->offsetExists('requestTime')) {
            $inputObject->offsetSet('requestTime', $this->convertElasticDate($inputObject->offsetGet('requestTime')));
        }
        if (!$inputObject->offsetExists('tags')) {
            $inputObject->offsetSet('tags', new ArrayObject());
        }
        return $inputObject;
    }

    public function exportRequestTime() : ?string
    {
        return (is_null($this->requestTime)) ? null: $this->requestTime->format('Y-m-d\TH:i:s.u');
    }

    public function addTag(string $tag) : self
    {
        $this->tags->offsetSet(null, $tag);
        return $this;
    }
}