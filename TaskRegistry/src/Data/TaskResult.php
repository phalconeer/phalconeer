<?php
namespace Phalconeer\TaskRegistry\Data;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\TaskRegistry as This;

class TaskResult extends Dto\ImmutableDto
{
    use Dto\Trait\ArrayObjectExporter,
        Dto\Trait\ArrayLoader,
        Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected static array $loadTransformers = [
        This\Transformer\GetNextIterationDetailObject::class,
        This\Transformer\GetExecutionTime::class,
        This\Transformer\InitializeFollowUpTasks::class,
        This\Transformer\RemoveExecutionDetail::class,
    ];

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
    ];

    protected ?Dto\ArrayObjectExporterInterface $executionDetail;

    protected float $executionTime;

    protected ?int $exceptionCode;

    protected ?This\Data\TaskExecutionCollection $followUpTasks;

    protected ?Dto\ArrayObjectExporterInterface $nextIterationDetail;

    protected ?string $nextIterationDetailClass;

    protected string $result;

    protected bool $success = true;

    protected string $task;

    public function setTask(string $task) : self
    {
        return $this->setValueByKey('task', $task);
    }

    public function addFollowUpTask(This\Data\TaskExecution $task) : self
    {
        $this->followUpTasks->offsetSet(null, $task);
        return $this;
    }
}