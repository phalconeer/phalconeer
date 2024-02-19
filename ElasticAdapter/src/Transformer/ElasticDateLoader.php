<?php
namespace Phalconeer\ElasticAdapter\Transformer;

use Phalconeer\Dto;

class ElasticDateLoader extends Dto\Transformer\AbstractDateLoader
{
    const TRAIT_METHOD = 'loadAllElasticDate';

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