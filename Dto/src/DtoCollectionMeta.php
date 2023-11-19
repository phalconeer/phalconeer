<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class DtoCollectionMeta extends Data\CollectionMeta implements Data\CollectionMetaInterface, This\DtoCollectionMetaInterface
{
    protected ?bool $convertChildren = true;

    protected ?array $exportTransformers = [];

    protected ?bool $preserveKeys = false;

    public function convertChildren() : bool
    {
        return $this->convertChildren;
    }

    public function exportTransformers() : array
    {
        return $this->exportTransformers;
    }

    public function preserveKeys() : bool
    {
        return $this->preserveKeys;
    }

    public function setConvertChildren(bool $convertChildren) : self
    {
        $this->convertChildren = $convertChildren;
        return $this;
    }

    public function setExportTransformers(array $exportTransformers) : self
    {
        $this->exportTransformers = $exportTransformers;
        return $this;
    } 

    public function appendExportTransformers(array $exportTransformers) : self
    {
        array_push($this->exportTransformers, $exportTransformers);
        return $this;
    } 

    public function prependExportTransformers(array $exportTransformers) : self
    {
        array_unshift($this->exportTransformers, $exportTransformers);
        return $this;
    } 

    public function setPreserveKeys(bool $preserveKeys) : self
    {
        $this->preserveKeys = $preserveKeys;
        return $this;
    }
}