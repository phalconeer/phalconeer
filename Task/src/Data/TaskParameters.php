<?php
namespace Phalconeer\Task\Data;

use Phalconeer\Data;

class TaskParameters extends Data\ImmutableData
{
    protected static $_properties = [
        'runDelay'                  => Data\Helper\ParseValueHelper::TYPE_INT,
    ];

    protected int $runDelay;

    public function runDelay() : ?int
    {
        return $this->getValue('runDelay');
    }

    public function setRunDelay(int $runDelay) : self
    {
        return $this->setValueByKey('runDelay', $runDelay);
    }
}