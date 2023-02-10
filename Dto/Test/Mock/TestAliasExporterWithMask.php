<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestAliasExporterWithMask extends This\Mock\Test
{
    use Dto\Traits\AliasExporter;

    protected static array $_properties = [
        'nestedObject'          => TestAliasExporterWithMask::class,
    ];

    protected static array $_exportAliases = [
        'stringProperty'            => 'externalStringProperty',
        'boolProperty'              => null,
    ];

    protected static array $_exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_ALIAS,
    ];
}