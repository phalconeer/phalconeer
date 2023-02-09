<?php

namespace Phalconeer\ElasticAdapter\Helper;

class ElasticQueryBodyHelper
{
    //@TODO: not yet implemented, it will be used to determine how to match a field
    const MATCH_TYPE_EXACT = 'exact';
    
    //@TODO: not yet implemented, it will be used to determine how to match a field
    const MATCH_TYPE_FULLTEXT = 'fulltext'; //Works only on analyzed fields, use match and query context;
    
    //@TODO: maybe unnecessray, as this is the default behaviour if nothing is supplied.
    const ALL_FIELDS = '_all'; //Add to a match leaf, to run the query on all fields in a document.

    /**
     * Use this property to set the starting position of the returned data.
     * Default is 0.
     */
    const NODE_FROM = 'from';
    
    /**
     * Use this property to define the chunk size of the returned data.
     * Default is 10.
     * If set to 0, the header of the query result will be returned. It is useful for finding out the amount of hits.
     */
    const NODE_SIZE = 'size';
    
    /**
     * Sets a maximum time limit for the execution. If the limit is reached, the partial result is returned.
     */
    const NODE_TIMEOUT = 'timeout';
    
    /**
     * Defines a list of source fields. List is a comma separated list of field names.
     * '_source' and 'fields' can not appear in the same query.
     * If the list is supplied, the results are returnes as a flat list, where the nested fields are represented by their dot concatenated hiearachical name.
     */
    const NODE_FIELDS = '_source';
    
    /**
     * Contains the requirements for sorting. It can include a series of fields, as a comma separated list.
     * Use '_score' for returning results in the order of the match score.
     * Use '_doc' for returning results in their natural storge order (as they have been entered to the database)
     */
    const NODE_SORT = 'sort';
    
    const TERM_ASCENDING = 'asc';
    
    const TERM_DESCENDING = 'desc';

    /**
     * Used when querying with a scroll_id, to set the TTL for the next scroll chunk.
     */
    const NODE_SCROLL = 'scroll';
    
    /**
     * If a scroll query is setup, this is the variable to send the scroll_id through.
     */
    const NODE_SCROLL_ID = 'scroll_id';
    
    /**
     * If set, the result collection ends, after this number of documents have been processed.
     */
    const NODE_TERMINATE_AFTER = 'terminate_after';

    /**
     * Root objct of the search criteria. All the different criteria are nested below this node.
     * https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl.html
     * There is a simplified way of using query parameter, when there is only field queried with one search value: "[fieldName]" : "[query]".
     */
    const NODE_QUERY = 'query';
    
    /**
     * Root obejct for filtering queries (basically where no full text search is required.
     */
    const NODE_FILTER = 'filter';

    /**
     * This node cintains the rules for aggregations.
     */
    const NODE_AGGS = 'aggs';

    /**
     * This node defines an aggregation where only the best matches are shown
     */
    const NODE_TOP_HITS = 'top_hits';

    /**
     * This node defines what fields are returned within the bucket
     */
    const NODE__SOURCE = '_source';

    /**
     * This node defines what fields are included in the source
     */
    const NODE__SOURCE_INCLUDES = 'includes';

    /**
     * This node defines a special date histrogram bucket within aggregation
     */
    const NODE_DATE_HISTOGRAM = 'date_histogram';

    /**
     * This node defines the interval of the date_histogram bucket aggregation
     */
    const NODE_CALENDAR_INTERVAL = 'calendar_interval';

    /**
     * This node defines the output format of the date keys for the date_inteval buckets.
     * Default is yyyy-MM-ddTH:m:s
     */
    const NODE_FORMAT = 'format';
    
    /**
     * If the query contains any match type condition, this will be the limit below which a result is not considered match.
     */
    const NODE_MIN_SCORE = 'min_score';
    
    /**
     * If this is added to the body of a search call, all items will return the sequence and primary term information as well
     * https://www.elastic.co/guide/en/elasticsearch/reference/7.17/optimistic-concurrency-control.html
     */
    const NODE_SEQ_NO_PRIMARY_TERM = 'seq_no_primary_term';

    /**
     * Used as a leaf, it returns all docuemnts. If in query context it returns with a score of 1.0.
     * It is rarely required as an empty condition search has the same result.
     */
    const QUERY_MATCH_ALL = 'match_all';
    
    /**
     * This leaf is used for fulltext matching. 
     */
    const QUERY_MATCH = 'match'; //A query type, running in query context.
    
    /**
     * Use this leaf to match a string against multiple fields.
     */
    const QUERY_MULTI_MATCH = 'multi_match'; //Same as match, can run the query on multiple fileds.
    
    /**
     * This nodes is the root for aggregating a list of search criteria. 
     */
    const QUERY_TYPE_BOOL = 'bool';
    
    /**
     * Alias for 'bool' type, when the criteria within the node are in a logic 'and' relation.
     * It is a shortcut of setting the 'operator' to 'and' within all the child criteria.
     */
    const QUERY_TYPE_BOOL_AND = 'and';
    
    /**
     * Alias for 'bool' type, when the criteria within the node are in a logic 'or' relation.
     * This is the default behaviour of no 'operator' is defined in the child criteria.
     */
    const QUERY_TYPE_BOOL_OR = 'or';
    
    /**
     * Alias for 'bool' type, it filters out the all the docuements matching the child criteria, and returns the rest.
     * @TODO: test what happens when multiple criteria is supplied. Educated guess: criteria are in 'or' relation by default.
     */
    const QUERY_TYPE_BOOL_NOT = 'not';
    
    const QUERY_TYPE_BOOL_MUST = 'must';
    
    /**
     * Sets the logical operator of the node. Default value is 'or'.
     */
    const QUERY_OPERATOR = 'operator';

    /**
     * This node type indicates that a single field is the search target for the listed values.
     */
    const QUERY_TERM = 'term';
    
    /**
     * This node type indicates that there are multiple values to match against the field.
     */
    const QUERY_TERMS = 'terms';
    
    /**
     * This node type indicates that the query contains a lower and higher value, and the result is between them.
     * For one ended queries use '*' as the open ended value, or ommit the open ended value.
     */
    const QUERY_RANGE = 'range';
    
    /**
     * This node in a query indicates the value.
     */
    const QUERY_VALUE = 'value';
    
    /**
     * Used when querying nested objects. They can be recognized by a '.' in the filed name.
     */
    const QUERY_NESTED = 'nested';
    
    /**
     * Used in nested queries to mark the root object of the nested queries, within the parent document.
     */
    const QUERY_PATH = 'path';
    
    /**
     * Used when chacking for a field being not null.
     */
    const QUERY_EXISTS = 'exists';
    
    /**
     * @TODO: What does thies field do exactly?
     */
    const QUERY_ROOT = 'root';
    
    /**
     * This is used when we query on parent documents with a property of their child
     */
    const QUERY_HAS_CHILD = 'has_child';
    
    /**
     * This is used when the child / parent type has to be defined
     */
    const QUERY_TYPE = 'type';
    
    /**
     * This is used in nested queries to retrieve the nested content as well
     */
    const QUERY_INNER_HITS = 'inner_hits';
    
    /**
     * Used in aggregation to signal the fild to aggregate by
     */
    const QUERY_FIELD = 'field';
    
    /**
     * Type of aggregation to sum values
     */
    const QUERY_AGGREGATE_SUM = 'sum';

    /**
     * List of nodes which are valid within the application.
     * @var array 
     */
    public static $allowedNodes = [
        self::NODE_QUERY,
        self::NODE_FILTER,
        self::NODE_FROM,
        self::NODE_SIZE,
        self::NODE_SORT,
        self::NODE_FIELDS,
        self::NODE_TIMEOUT,
        self::NODE_TERMINATE_AFTER,
        self::NODE_SCROLL,
        self::NODE_SCROLL_ID,
        self::NODE_AGGS,
        self::NODE_MIN_SCORE,
    ];

    public static function getAggregate(string $field, $size = 10) : array
    {
        return [
            self::QUERY_TERMS     => [
                self::QUERY_FIELD       => $field,
                self::NODE_SIZE         => $size
            ]
        ];
    }

    public static function getInterval(string $interval) : \DateInterval
    {
        preg_match('/^(\-\+?)(\d+)([\D]?)$/', $interval, $matches);

        $internalInterval = 'P';
        $signIncluded = count($matches) === 4;
        $pastTime = ($signIncluded && $matches[1] === '-')
            ? true
            : false;
        $period = ($signIncluded)
            ? $matches[2]
            : $matches[1];
        $periodIndicator = ($signIncluded)
            ? $matches[3]
            : $matches[2];
        switch ($periodIndicator) {
            case 'm':
                $internalInterval .= 'T' . $period . 'M';
                break;
        }

        $internalIntervalObject = new \DateInterval($internalInterval);
        if ($pastTime) {
            $internalIntervalObject->invert = 1;
        }

        return $internalIntervalObject;
    }
}