<?php
namespace Phalconeer\ElasticAdapter\Transformer;

use Phalconeer\Dto;

class ElasticDateExporter extends Dto\Transformer\AbstractDateExporter
{
    const TRAIT_METHOD = 'exportAllElasticDate';

    public static function convertDate(
        \DateTime $date
    ) : string 
    {
        return $date->format('c');
    }
}