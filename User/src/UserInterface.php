<?php
namespace Phalconeer\User;

use Phalconeer\Dto;

interface UserInterface extends Dto\ArrayObjectExporterInterface
{
    public static function getSensitiveProperties() : array;

    public function id() : ?int;

    public function setLastLogin(\DateTime $lastLogin) : self;
}