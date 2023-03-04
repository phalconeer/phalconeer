<?php
namespace Phalconeer\User;

use Phalconeer\Dto;

interface UserInterface extends Dto\ArrayObjectExporterInterface
{
    public static function getSensitiveProperties() : array;

    public function setLastLogin(\DateTime $lastLogin) : self;
}