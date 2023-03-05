<?php
namespace Phalconeer\LiveSession\Data;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\MySqlAdapter;

class LiveSession extends Dto\ImmutableDto
{
    use MySqlAdapter\Trait\MySqlDateExporter,
        Dto\Trait\ArrayLoader,
        Dto\Trait\ArrayObjectExporter,
        Data\Trait\Data\ParseTypes,
        Data\Trait\Data\AutoGetter;

    protected string $id;
    
    protected string $userId;

    protected \DateTime $expires;

    protected \ArrayObject $scopes;

    protected \ArrayObject $deniedPermissions;
    
    public function setExpires(\DateTime $expires)
    {
        return $this->setValueByKey('expires', $expires);
    }
    
    public function setId(string $id) : self
    {
        return $this->setValueByKey('id', $id);
    }

    public function timeToExpire(\DateTime $dateTime = null) : ?\DateInterval
    {
        if (!$this->expires instanceof \DateTime) {
            return null;
        }
        if (is_null($dateTime)) {
            $dateTime = new \DateTime();
        }

        return $dateTime->diff($this->expires); // invert === 1 means past
    }
}