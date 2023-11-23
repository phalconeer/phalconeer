<?php
namespace Phalconeer\Dto;

use Phalconeer\Dto as This;

class TransfromerMeta implements This\TransformerMetaInterface
{
    protected ?bool $convertChildren = true;

    protected ?array $exportAliases = [];

    protected ?array $exportTransformers = [];

    protected ?array $loadAliases = [];

    protected ?array $loadTransformers = [];

    protected ?bool $preserveKeys = false;

    public function convertChildren() : bool
    {
        return $this->convertChildren;
    }

    public function exportAliases() : array
    {
        return $this->exportAliases;
    }

    public function exportTransformers() : array
    {
        return $this->exportTransformers;
    }

    public function loadAliases() : array
    {
        return $this->loadAliases;
    }

    public function loadTransformers() : array
    {
        return $this->loadTransformers;
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
        array_push($this->exportTransformers, ...$exportTransformers);
        return $this;
    } 

    public function prependExportTransformers(array $exportTransformers) : self
    {
        array_unshift($this->exportTransformers, ...$exportTransformers);
        return $this;
    } 

    public function setLoadTransformers(array $loadTransformers) : self
    {
        $this->loadTransformers = $loadTransformers;
        return $this;
    } 

    public function setExportAliases(array $exportAliases) : self
    {
        $this->exportAliases = $exportAliases;
        return $this;
    } 

    public function addExportAliases(array $exportAliases) : self
    {
        $this->exportAliases = array_merge($this->exportAliases, $exportAliases);
        return $this;
    } 

    public function setLoadAliases(array $loadAliases) : self
    {
        $this->loadAliases = $loadAliases;
        return $this;
    }

    public function setPreserveKeys(bool $preserveKeys) : self
    {
        $this->preserveKeys = $preserveKeys;
        return $this;
    }
}