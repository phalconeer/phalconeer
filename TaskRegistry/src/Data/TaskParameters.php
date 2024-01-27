<?php
namespace Phalconeer\TaskRegistry\Data;

use Phalconeer\Data;
use Phalconeer\Dto;

class TaskParameters extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayObjectExporter,
        Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
    ];

    protected static $_properties = [
        'runDelay'                  => Data\Helper\ParseValueHelper::TYPE_INT,
    ];

    protected ?int $runDelay;

    public function runDelay() : ?int
    {
        return $this->getValue('runDelay');
    }

    public function setRunDelay(int $runDelay) : self
    {
        return $this->setValueByKey('runDelay', $runDelay);
    }
}