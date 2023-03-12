<?php
namespace Phalconeer\Exception\Export;

use Phalconeer\Data;
use Phalconeer\Exception as This;

class ExceptionTraceCollection extends Data\ImmutableCollection
{
  protected string $collectionType = This\Export\ExceptionTrace::class;
}