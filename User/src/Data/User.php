<?php
namespace Phalconeer\User\Data;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\User as This;

class User extends Dto\ImmutableDto implements This\UserInterface
{
    use Dto\Trait\ArrayObjectExporter,
        Data\Trait\ParseTypes,
        Data\Trait\AutoGetter;

    protected static array $sensitiveProperties = [];

    protected ?int $id = null;

    protected ?\DateTime $lastLogin = null;

    protected ?\DateTime $signupDate = null;

    public static function getSensitiveProperties() : array
    {
        $parentClassName = get_parent_class(static::class);
        return method_exists($parentClassName, __FUNCTION__) ? 
            array_merge($parentClassName::getSensitiveProperties(), static::$sensitiveProperties) : 
            static::$sensitiveProperties;
    }

    public function setLastLogin(\DateTime $lastLogin) : self
    {
        return $this->setValueByKey('lastLogin', $lastLogin);
    }

    public function getPrimaryKey() : array
    {
        return ['id'];
    }
}