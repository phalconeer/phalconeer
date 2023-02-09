<?php
namespace Phalconeer\ElasticAdapter\Helper;

/**
 * Important node names within Elastic responses.
 *
 * @author lordg
 */
class ElasticResponseHelper
{
    /**
     * This
     */
    const NODE_RESULT = 'result';

    /**
     * This
     */
    const NODE_ERROR = 'error';

    /**
     * If the request has been requested as a scroll, this field contains the scroll id to continue.
     */
    const NODE_SCROLL_ID = '_scroll_id';
     
    /**
     * Contains information about the effected elastic shards.
     */
    const NODE_SHARDS = '_shards';
    
    /**
     * Total number of hits.
     */
    const NODE_HITS_TOTAL = 'total';
    
    /**
     * Total number of hits.
     */
    const NODE_HITS_MAX_SCORE = 'max_score';
    
    /**
     * Contains information about the matched docuements.
     * List of real hits are found in the 'hits' node of this node.
     */
    const NODE_HITS = 'hits';
    
    /**
     * Contains documents inserted into ELasztic directly..
     */
    const NODE_ITEMS = 'items';
  
    /**
     * Used when indexing one document, true is it was created.
     */
    const NODE_CREATED = 'created';
   
    /**
     * Contains the index information if only one item is returned.
     */
    const NODE_INDEX = '_index';
    
    /**
     * Contains the type information if only one item is returned.
     */
    const NODE_TYPE = '_type';
    
    /**
     * Contains the id information if only one item is returned.
     */
    const NODE_ID = '_id';
    
    /**
     * Contains the version information if only one item is returned.
     */
    const NODE_VERSION = '_version';

    /**
     * If there were no field list supplied, the results are found under this node.
     */
    const NODE_HITS_SOURCE = '_source';
    
    /**
     * If the request had a field list supplied, the results are found under this node.
     */
    const NODE_HITS_FIELDS = 'fields';
    
    /**
     * Used when searching by document id, it shows if the document is found.
     */
    const NODE_FOUND = 'found';

    /**
     * Used when searching by document id, it shows if the document is found.
     */
    const NODE_INNER_HITS = 'inner_hits';

    const NODE_AGGS = 'aggregations';
    
    const NODE_DOC_COUNT = 'doc_count';
    
    const NODE_DOC_COUNT_ERROR_UPPER_BOUND = 'doc_count_error_upper_bound';
    
    const NODE_SUM_OTHER_DOC_COUNT = 'sum_other_doc_count';
    
    const NODE_KEY = 'key';
    
    const NODE_VALUE = 'value';
    
    const NODE_BUCKETS = 'buckets';
    /**
     * Number of effected shards.
     */
    const SHARDS_TOTAL = 'total';
    
    /**
     * Number of shards where the query was successful.
     */
    const SHARDS_SUCCESSFUL = 'successful';
    
    /**
     * Number of shards where the query failed.
     */
    const SHARDS_FAILED = 'failed';

    /**
     * Found in the result node when a document is successfully created
     */
    const VALUE_CREATED = 'created';

    /**
     * Found in the result node when a document is successfully updated
     */
    const VALUE_UPDATED = 'updated';

    const NODE_NOT_AGGREGATE_DEFINITIONS = [
        self::NODE_KEY,
        self::NODE_DOC_COUNT,
    ];

    public static function convertDate(string $elasticDate) : ?\DateTime
    {
        return is_null($elasticDate)
            ? $elasticDate
            : new \DateTime($elasticDate);
    }
}