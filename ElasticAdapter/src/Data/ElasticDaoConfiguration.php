<?php
namespace Phalconeer\ElasticAdapter\Data;

use Phalcon\Config as PhalconConfig;
use Phalconeer\Browser;
use Phalconeer\Data;
use Phalconeer\Http;

class ElasticDaoConfiguration extends Data\ImmutableData
{
    use Data\Trait\ParseTypes,
        Data\Trait\AutoGetter;

    protected Browser\BrowserInterface $browser;

    protected ?PhalconConfig\Config $config = null;

    protected ?Http\Data\Uri $defaultUri = null;
}