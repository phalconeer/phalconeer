<?php
namespace Phalconeer\Task\Data;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\ElasticAdapter;
use Phalconeer\Task as This;

class TaskExecution extends ElasticAdapter\Data\ElasticBase
{
    use Dto\Trait\ArrayObjectExporter,
        ElasticAdapter\Trait\ElasticDateExporter,
        ElasticAdapter\Trait\ElasticDateLoader,
        Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;

    protected static array $exportTransformers = [
        Dto\Transformer\ArrayObjectExporter::TRAIT_METHOD,
        ElasticAdapter\Transformer\ElasticDateExporter::TRAIT_METHOD,
    ];

    protected static array $loadTransformers = [
        ElasticAdapter\Transformer\ElasticDateLoader::TRAIT_METHOD,
        This\Transformer\GenerateId::class,
        This\Transformer\GetDetailObject::class,
    ];

    protected ?\DateTime $actualRunTime;

    protected ?string $createdByTaskId;

    protected This\Data\TaskEnvironment $definedOn;

    protected ?Dto\ArrayObjectExporterInterface $detail;

    protected ?string $detailClass;

    protected ?This\Data\TaskEnvironment $executedOn;
    
    protected \DateTime $expectedRunTime;

    protected int $failCount = 0;

    protected int $iterationId = 0;

    protected int $priority = 0;

    protected ?This\Data\TaskResult $result;

    protected string $status = This\Helper\TaskHelper::STATUS_NEW;

    protected string $task;


    public function setStatus(string $status) : self
    {
        switch ($status) {
            case This\Helper\TaskHelper::STATUS_NEW:
            case This\Helper\TaskHelper::STATUS_PROCESSING:
            case This\Helper\TaskHelper::STATUS_DONE:
            case This\Helper\TaskHelper::STATUS_ERRORED:
            case This\Helper\TaskHelper::STATUS_FAILED:
            case This\Helper\TaskHelper::STATUS_CANCELLED:
                return $this->setValueByKey('status', $status);
        }

        return $this;
    }

    public function getDetailField(string $field)
    {
        return $this->detail?->$field();
    }

    public function setDetail(Dto\ArrayObjectExporterInterface $detail) : self
    {
        return $this->setKeyValue('detail', $detail);
    }

    public function setExpectedRunTime(\DateTime $expectedRunTime) : self
    {
        return $this->setKeyValue('expectedRunTime', $expectedRunTime);
    }

    public function setActualRunTime() : self
    {
        return $this->setKeyValue('actualRunTime', new \DateTime());
    }

    public function setDefinedOn() : self
    {
        return $this->setKeyValue('definedOn', This\Helper\TaskHelper::getServerDetails());
    }

    public function setExecutedOn() : self
    {
        return $this->setKeyValue('executedOn', This\Helper\TaskHelper::getServerDetails());
    }

    public function setIterationId(int $count) : self
    {
        return $this->setKeyValue('iterationId', $count);
    }

    public function incrementFailCount() : self
    {
        return $this->setKeyValue('failCount', $this->failCount + 1);
    }

    public function setFailCount(int $count) : self
    {
        return $this->setKeyValue('failCount', $count);
    }

    public function setCreatedBy(string $id) : self
    {
        return $this->setKeyValue('createdByTaskId', $id);
    }

    public function setResult(This\Data\TaskResult $result = null) : self
    {
        if (!is_null($result)
            && is_null($result->task())) {
            // This is needed to be able to decode the detail parameters
            $result = $result->setTask($this->task());
        }
        return $this->setKeyValue('result', $result);
    }
}