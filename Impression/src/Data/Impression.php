<?php
namespace Phalconeer\Impression\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

class Impression extends Dto\ImmutableDto implements Data\TagableInterface
{
    use Data\Trait\Data\ParseTypes,
        Data\Trait\Data\AutoGetter,
        Data\Trait\Tag;

    /**
     * Accepted MIME types of the visitor.
     */
    protected string  $accept;
    
    /**
     * Array representation of the body. Fulltext body is stored in MessageHelper::FULL_TEXT_BODY key
     */
    protected array $body;

    protected array $header;

    /**
     * Where the record were taken.
     */
    protected string $host;
    
    /**
     * IP of the visitor.
     */
    protected array $ip;
    
    /**
     * Language settings of the visitor's browser.
     */
    protected string $language;
    
    protected string $method;
    
    /**
     * Detailed URI of the request.
     */
    protected string $query;
    
    /**
     * Referer of the request-
     */
    protected string $referer;
    
    /**
     * Time of the request in Elastic domestic format.
     */
    protected \DateTime $requestTime;

    protected string $server;

    /**
     * User agent of visitor.
     */
    protected string $useragent;

    /**
     * IP of the visitor.
     */
    protected string $xForwarded;

    public function initializeData(\ArrayObject $inputObject) : \ArrayObject 
    {
        $inputObject = parent::initializeData($inputObject);
        if (!$inputObject->offsetExists('tags')) {
            $inputObject->offsetSet('tags', new \ArrayObject());
        }
        return $inputObject;
    }
}