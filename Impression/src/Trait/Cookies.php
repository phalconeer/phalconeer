<?php
namespace Phalconeer\Impression\Trait;

trait Cookies
{
    /**
     * Array representation of the body. Fulltext body is stored in MessageHelper::FULL_TEXT_BODY key
     */
    protected ?array $cookies;

    public function setCookies(array | string $cookies = null)
    {
        if (empty($cookies)) {
            return $this->setValueByKey('cookies', null);
        }

        if (!is_array($cookies)) {
            $cookies = explode('; ', $cookies);
        }
        return $this->setValueByKey('cookies', $cookies);
    }
}