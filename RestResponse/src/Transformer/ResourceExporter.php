<?php
namespace Phalconeer\RestResponse\Transformer;

use Phalconeer\Dto;

class ResourceExporter extends Dto\TransfromerMeta
{
    protected ?array $exportTransformers = [
        Dto\Transformer\ArrayExporter::TRAIT_METHOD,
    ];
}