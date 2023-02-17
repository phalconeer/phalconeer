<?php
namespace Phalconeer\LiveSession\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

class LiveSession extends Dto\ImmutableDto
{
    use Dto\Traits\MySqlDateExporter,
        Dto\Traits\ArrayLoader,
        Data\Traits\Data\ParseTypes,
        Data\Traits\Data\AutoGetter;

    protected string $id;
    
    protected string $userId;

    protected \DateTime $expires;

    protected array $scopes;

    protected array $deniedPermissions;
    
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