<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;

interface DtoMetaInterface extends Data\MetaInterface
{
    public function exportAliases() : array;

    public function exportTransformers() : array;

    public function loadAliases() : array;

    public function loadTransformers() : array;

    public function setExportTransformers(array $exportTransformers) : self;

    public function appendExportTransformers(array $exportTransformers) : self;

    public function prependExportTransformers(array $exportTransformers) : self;

    public function setLoadTransformers(array $loadTransformers) : self;

    public function setExportAliases(array $exportAliases) : self;

    public function addExportAliases(array $exportAliases) : self;

    public function setLoadAliases(array $loadAliases) : self;
}