<?php

namespace Phalconeer\ElasticAdapter\Helper;

use Phalcon\Text;
use Phalconeer\Http;

/**
 * This file contains the settings for Elastic search queries, which is passed through the query parameters.
 *
 * @author ikarasz
 */
class ElasticQueryRouteHelper
{
    /**
     * Placeholder left over from the time when the type was still a thing. This is ised on index API, when UPDATING records
     */
    const URI_DOC_TYPE = '_doc';

    /**
     * Placeholder left over from the time when the type was still a thing. This is ised on index API, when CREATING records
     */
    const URI_CREATE_TYPE = '_create';

    /**
     * this is the route required to delete from indices by query
     */
    const URI_DELETE_BY_QUERY = '_delete_by_query';

    /**
     * A query string, this is used by kibana when we enter something in the search field.
     * Uses the simplified Query Dsl syntax.
     * https://www.elastic.co/guide/en/elasticsearch/reference/2.3/query-dsl-query-string-query.html
     */
    const VAR_QUERY_STRING = 'q';

    /**
     * Defines a list of source fields. List is a comma separated list of field names.
     * '_source' and 'fields' can not appear in the same query.
     * If the list is supplied, the results are returned as a hierarchical array in case of nested objects.
     */
    const VAR_SOURCE = '_source';
    
    /**
     * Defines a list of source fields. List is a comma separated list of field names.
     * '_source' and 'fields' can not appear in the same query.
     * If the list is supplied, the results are returnes as a flat list, where the nested fields are represented by their dot concatenated hiearachical name.
     */
    const VAR_FIELDS = 'fields';
    
    /**
     * Sets if the cache is used for size=0 queries (also count and aggreagtion queries)
     * Defaults to false.
     */
    const VAR_REQUEST_CACHE = 'request_cache';
    
    /**
     * Sets the search type. https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-search-type.html
     * Defaults to 'query_then_fetch'
     */
    const VAR_SEARCH_TYPE = 'search_type';

    /**
     * If set to a time string, it will leave the search context open for that time and enable continues retrieval of additional pages.
     */
    const VAR_SCROLL = 'scroll';

    /**
     * This value controls how many result rows are returned per request. Default is 10.
     */
    const VAR_SIZE = 'size';

    /**
     * Time until a serch context is open after the query.
     */
    const DEFAULT_SCROLL_INTERVAL = '3m';

    /**
     * In case of multiple index queries, it will ignore if an index is missing.
     */
    const VAR_IGNORE_UNAVAILBLE = 'ignore_unavailable';
    
    /**
     * This parameter is required for querying child documents through their Ids. This parameter is used as routing.
     */
    const VAR_PARENT = 'parent';
    
    /**
     * This parameter is required for adding and deleting child documents.
     */
    const VAR_ROUTING = 'routing';
    
    /**
     * Makes sure that update operation only happens if the doc doc has been unchanged since the last read
     * This contains the information how many times the master shard ahs changed
     * Use together if_seq_no
     */
    const VAR_IF_PRIMARY_TERM = 'if_primary_term';
    
    /**
     * Makes sure that update operation only happens if the doc doc has been unchanged since the last read
     * This contains the information how many times the index has been changed
     * Use together if_primary_term
     */
    const VAR_IF_SEQ_NO = 'if_seq_no';
    
    /**
     * keys which are allowed within the query parameters of the Elastic search query.
     * @var array 
     */
    public static $allowedVariables = [
        self::VAR_FIELDS,
        self::VAR_SOURCE,
        self::VAR_QUERY_STRING,
        self::VAR_REQUEST_CACHE,
        self::VAR_SEARCH_TYPE,
        self::VAR_SCROLL,
        self::VAR_IGNORE_UNAVAILBLE,
        self::VAR_SIZE,
        self::VAR_PARENT,
        self::VAR_ROUTING
    ];
    
    /**
     * 
     * @param array $data
     * @param type $prefix
     * @return type
     */
    public static function filterAllowed($data, $criteria = null, $prefix = '')
    {
        if (is_null($criteria)) {
            $criteria = self::$allowedVariables;
        }
        foreach ($data as $key => $value) {
            $allowed = array_reduce($criteria, function($allowed, $current) use ($value, $prefix) {
                return ($allowed ||
                        Text::startsWith($value, $prefix . $current));
            }, false);
            if (!$allowed) {
                unset($data[$key]);
            }
        }
        
        return array_values($data);
    }

    /**
     * Elastic uses different URIs when creating and updating records, this helper returns the correct URI
     */
    public static function generateIndexUri($newRecord = false, $id = null) : array
    {
        switch (true) {
            case $newRecord && empty($id): // Create new document with autogenerated ID
                return [
                    Http\Helper\HttpHelper::HTTP_METHOD_POST,
                    implode('/', [
                        '',
                        self::URI_DOC_TYPE
                    ])
                ];
            case $newRecord && !empty($id): // Create new document with known ID
                return [
                    Http\Helper\HttpHelper::HTTP_METHOD_PUT,
                    implode('/', [
                        '',
                        self::URI_CREATE_TYPE,
                        $id
                    ])
                ];
            case !$newRecord && !empty($id): // Update existing document
                return [
                    Http\Helper\HttpHelper::HTTP_METHOD_PUT,
                    implode('/', [
                        '',
                        self::URI_DOC_TYPE,
                        $id
                    ])
                ];
        }
    }
}