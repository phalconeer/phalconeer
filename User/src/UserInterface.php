<?php
namespace Phalconeer\User;

use Phalconeer\Dto;

interface UserInterface extends Dto\ArrayObjectExporterInterface
{
    public function id() : ?int;

    public function setLastLogin(\DateTime $lastLogin) : self;
}