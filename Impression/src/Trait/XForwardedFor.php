<?php
namespace Phalconeer\Impression\Trait;

trait XForwardedFor
{
    /**
     * IP of the visitor.
     */
    protected ?string $xForwarded;
}