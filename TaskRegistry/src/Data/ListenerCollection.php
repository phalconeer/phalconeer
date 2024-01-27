<?php
namespace Phalconeer\TaskRegistry\Data;

use Phalconeer\Data;

class ListenerCollection extends Data\ImmutableCollection
{
    protected string $collectionType = Listener::class;
}