<?php
namespace Phalconeer\CurlClient;

use Psr;

interface CurlClientInterface extends Psr\Http\Client\ClientInterface
{
    public function withOption($key, $value): void;

    public function resetOptions() : void;
}