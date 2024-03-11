<?php
namespace Phalconeer\MySqlAdapter\Transformer;

use Phalconeer\Dto;

class MySqlDateExporter extends Dto\Transformer\AbstractDateExporter
{
    public static function convertDate(
        \DateTime $date
    ) : string 
    {
        return $date->format('Y-m-d H:i:s');
    }
}