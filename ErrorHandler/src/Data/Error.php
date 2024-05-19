<?php
namespace Phalconeer\ErrorHandler\Data;

use Phalconeer\ErrorHandler as This;
use Phalconeer\Data;
use Phalconeer\Dto;

class Error extends Dto\ImmutableDto
{
    use This\Trait\ErrorNumber,
        This\Trait\File,
        This\Trait\Globals,
        This\Trait\Id,
        This\Trait\Line,
        This\Trait\Message,
        This\Trait\Server,
        This\Trait\Trace,
        Dto\Trait\ArrayLoader,
        Data\Trait\AutoGetter,
        Data\Trait\ParseTypes;
}
