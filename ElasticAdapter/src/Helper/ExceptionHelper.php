<?php
namespace Phalconeer\ElasticAdapter\Helper;

use Phalconeer\Exception;
use Phalconeer\ElasticAdapter as This;

/**
 * Module exception code: 40
 */
class ExceptionHelper
{
    const ELASTIC__UNKNWON_ERROR                                = 140105001;

    const ELASTIC__MAPPING_ERROR                                = 140105002;

    const ELASTIC__INDEX_NOT_FOUND_ERROR                        = 140105003;

    const ELASTIC__VERSION_CONFLICT                             = 140105003;

    const ELASTIC_QUERY_BUILDER__INVALID_RANGE                  = 140200001;

    const ELASTIC_QUERY_BUILDER__INVALID_CONDITION              = 140200002;

    const ELASTIC_DAO_BASE__INVALID_BROWSER_INSTANCE            = 140300001;

    const ELASTIC_DAO_BASE__INVALID_DATA_OBJECT_TO_SAVE         = 140300002;

    const NODE_ERROR_TYPE = 'type';

    const NODE_ERROR_REASON = 'reason';

    const ERROR_TYPE_MAPPING_ERROR = 'mapper_parsing_exception';

    const ERROR_TYPE_INDEX_NOT_FOUND_ERROR = 'index_not_found_exception';

    const ERROR_TYPE_VERSION_CONFLICT = 'version_conflict_engine_exception';

    public static function handleException(array $error)
    {
    // echo \Phalconeer\Helper\TVarDumper::dump($error);
        switch ($error[self::NODE_ERROR_TYPE]) {
            case self::ELASTIC__MAPPING_ERROR:
                throw new This\Exception\ElasticMappingException($error[self::NODE_ERROR_REASON], self::ELASTIC__MAPPING_ERROR);
            case self::ERROR_TYPE_INDEX_NOT_FOUND_ERROR:
                throw new This\Exception\ElasticIndexNotFoundException($error[self::NODE_ERROR_REASON], self::ELASTIC__INDEX_NOT_FOUND_ERROR);
            case self::ERROR_TYPE_VERSION_CONFLICT:
                throw new This\Exception\ElasticVersionConflictException($error[self::NODE_ERROR_REASON], self::ELASTIC__VERSION_CONFLICT);
            default:
                throw new Exception\Exception($error[self::NODE_ERROR_REASON], self::ELASTIC__UNKNWON_ERROR);
        }
    }

// array
// (
//     [root_cause] => array
//     (
//         [0] => array
//         (
//             [type] => 'query_shard_exception'
//             [reason] => 'No mapping found for [name] in order to sort on'
//             [index_uuid] => 'u4oUQgrOTLSAQ268rSo-dA'
//             [index] => 'adgn-tasklog-crucian-2020.12'
//         )
//     )
//     [type] => 'search_phase_execution_exception'
//     [reason] => 'all shards failed'
//     [phase] => 'query'
//     [grouped] => true
//     [failed_shards] => array
//     (
//         [0] => array
//         (
//             [shard] => 0
//             [index] => 'adgn-tasklog-crucian-2020.12'
//             [node] => 'DU_4kABXRUaHF9JjsNq-rA'
//             [reason] => array
//             (
//                 [type] => 'query_shard_exception'
//                 [reason] => 'No mapping found for [name] in order to sort on'
//                 [index_uuid] => 'u4oUQgrOTLSAQ268rSo-dA'
//                 [index] => 'adgn-tasklog-crucian-2020.12'
//             )
//         )
//     )
// )

}