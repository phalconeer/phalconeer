<?php
namespace Phalconeer\Impression\Trait;

use Phalconeer\Http;

trait Body
{
    /**
     * Array representation of the body. Fulltext body is stored in MessageHelper::FULL_TEXT_BODY key
     */
    protected ?array $body;

    public function setBody(array | string $body = null)
    {
        if (empty($body)) {
            return $this->setValueByKey('body', null);
        }

        if (!is_array($body)) {
            $body = [
                Http\Helper\MessageHelper::FULL_TEXT_BODY_ELASTIC => $body
            ];
        }
        return $this->setValueByKey('body', $body);
    }
}