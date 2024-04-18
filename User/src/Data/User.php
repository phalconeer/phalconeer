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

    protected ?int $id = null;

    protected ?\DateTime $lastLogin = null;

    protected ?\DateTime $signupDate = null;

    public function getPrimaryKey() : array
    {
        return ['id'];
    }

    public function id() : ?int
    {
        return $this->getValue('id');
    }

    public function setLastLogin(\DateTime $lastLogin) : self
    {
        return $this->setValueByKey('lastLogin', $lastLogin);
    }
}