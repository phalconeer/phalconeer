<?php
namespace Phalconeer\Application\Data;

use Phalconeer\Data;

class Application extends Data\ImmutableData
{
    use Data\Trait\ParseTypes,
        Data\Trait\AutoGetter;
    
    protected int $id = 0;

    protected string $name = 'phalconeer';

    protected string $privilegeScheme = 'ph';

    protected string $version = 'DEV';

    public function setVersion(string $version = 'DEV') : self
    {
        return $this->setValueByKey('version', $version);
    }
}