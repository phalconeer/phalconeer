<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto;
use Phalconeer\Dto\Test as This;

class TestAliasExporterConvertFalse extends This\Mock\Test
{
    use Dto\Traits\AliasExporter;

    protected static bool $_convertChildren = false;

    protected static array $_properties = [
        'nestedObject'          => TestAliasExporterConvertFalse::class,
    ];

    protected static array $_exportAliases = [
        'stringProperty'            => 'externalStringProperty'
    ];

    protected static array $_exportTransformers = [
        Dto\Helper\TraitsHelper::EXPORT_METHOD_ALIAS,
    ];
}