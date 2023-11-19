<?php
namespace Phalconeer\Dto;

use Phalconeer\Data;

interface DtoCollectionMetaInterface extends Data\CollectionMetaInterface
{
    public function convertChildren() : bool;

    public function exportTransformers() : array;

    public function preserveKeys() : bool;

    public function setConvertChildren(bool $convertChildren) : self;

    public function setExportTransformers(array $exportTransformers) : self;

    public function appendExportTransformers(array $exportTransformers) : self;

    public function prependExportTransformers(array $exportTransformers) : self;

    public function setPreserveKeys(bool $preserveKeys) : self;
}