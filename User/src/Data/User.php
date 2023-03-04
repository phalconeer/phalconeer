<?php
namespace Phalconeer\User\Data;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\User as This;

class User extends Dto\ImmutableDto implements This\UserInterface
{
    use Dto\Trait\ArrayObjectExporter,
        Data\Trait\Data\ParseTypes,
        Data\Trait\Data\AutoGetter;

    protected static array $_sensitiveProperties = [];

    protected array $_sensitivePropertiesCache = [];

    protected static array $_internalProperties = [
        '_sensitivePropertiesCache',
    ];

    protected ?int $id = null;

    protected ?\DateTime $lastLogin = null;

    protected ?\DateTime $signupDate = null;

    public static function getSensitiveProperties() : array
    {
        $parentClassName = get_parent_class(static::class);
        return method_exists($parentClassName, __FUNCTION__) ? 
            array_merge($parentClassName::getSensitiveProperties(), static::$_sensitiveProperties) : 
            static::$_sensitiveProperties;
    }

    public function __construct(array $input = null, \ArrayObject $inputObject = null)
    {
        parent::__construct($input, $inputObject);

        $this->_sensitivePropertiesCache = $this->getSensitiveProperties();
    }

    public function setLastLogin(\DateTime $lastLogin) : self
    {
        return $this->setValueByKey('lastLogin', $lastLogin);
    }
}