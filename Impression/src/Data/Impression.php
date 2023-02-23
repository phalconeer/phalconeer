<?php
namespace Phalconeer\Impression\Data;

use Phalconeer\Data;
use Phalconeer\Dto;
use Phalconeer\Impression as This;

class Impression extends Dto\ImmutableDto implements This\ImpressionInterface
{
    use Data\Trait\Data\ParseTypes,
        Data\Trait\Data\AutoGetter,
        Data\Trait\Tag,
        Dto\Trait\AliasExporter,
        This\Trait\Accept,
        This\Trait\Body,
        This\Trait\Header,
        This\Trait\Host,
        This\Trait\Ip,
        This\Trait\Language,
        This\Trait\Method,
        This\Trait\Query,
        This\Trait\Referer,
        This\Trait\RequestTime,
        This\Trait\Server,
        This\Trait\UserAgent,
        This\Trait\XForwardedFor;
}