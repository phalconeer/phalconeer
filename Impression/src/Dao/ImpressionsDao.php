<?php
namespace Phalconeer\Module\Impression\Dao;

use Phalconeer\Module\ElasticAdapter\Dao\ElasticDaoBase;
use Phalconeer\Module\ElasticAdapter\Helper\IndexVersioningHelper;

class ImpressionsDao extends ElasticDaoBase
{
    protected $indexName = 'impression-*';
}