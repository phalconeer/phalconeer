<?php
namespace Phalconeer\MySqlAdapter\Transformer;

use Phalconeer\Dto;

class MySqlDateLoader extends Dto\Transformer\AbstractDateLoader
{
    const TRAIT_METHOD = 'loadAllMySqlDate';

    public static function convertDate(
        $date
    ) : ?\DateTime 
    {
        if (is_null($date)
            || $date instanceof \DateTime) {
            return $date;
        }

        return new \DateTime($date);
    }
}