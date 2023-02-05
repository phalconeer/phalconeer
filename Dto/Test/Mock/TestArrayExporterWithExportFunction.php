<?php
namespace Phalconeer\Dto\Test\Mock;

use Phalconeer\Dto\Test as This;

class TestArrayExporterWithExportFunction extends This\Mock\TestArrayExporter
{
    
    protected static array $_properties = [
        'nestedObject'          => TestArrayExporterWithExportFunction::class,
    ];

    public function exportStringProperty() : string
    {
        return 'This is the exported value';
    }
}