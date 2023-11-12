<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;
use Phalconeer\Dto as This;

class DtoMeta extends Data\DataMeta implements Data\MetaInterface, This\DtoMetaInterface
{
    protected array $exportAliases = [];

    protected array $exportTransformers = [];

    protected array $loadAliases = [];

    protected array $loadTransformers = [];

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
}