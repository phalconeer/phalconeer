<?php

namespace Phalconeer\Exception;

interface ExceptionInterface
{
    public function getComponent() : string;
}