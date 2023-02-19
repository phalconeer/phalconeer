<?php
namespace Phalconeer\Application;

interface ApplicationInterface
{
    public function getId() : int;

    public function getName() : ?string;

    public function getPrivilegeScheme() : ?string;

    public function getVersion() : ?string;
}