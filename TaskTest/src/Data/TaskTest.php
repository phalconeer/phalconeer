<?php
namespace Phalconeer\TaskTest\Data;

use Phalconeer\Data;
use Phalconeer\Task;

class TaskTest extends Task\Data\TaskParameters
{
    protected static $_properties = [
        'message'                  => Data\Helper\ParseValueHelper::TYPE_STRING,
    ];

    protected string $message;


    public function message() : ?string
    {
        return $this->message;
    }
}