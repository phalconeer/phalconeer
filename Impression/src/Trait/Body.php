<?php
namespace Phalconeer\Impression\Trait;

trait Body
{
    /**
     * Array representation of the body. Fulltext body is stored in MessageHelper::FULL_TEXT_BODY key
     */
    protected ?array $body;

    public function setBody(array $body = null)
    {
        return $this->setValueByKey('body', $body);
    }
}