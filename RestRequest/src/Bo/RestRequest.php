<?php
namespace Phalconeer\RestRequest\Bo;

use Phalcon\Config as PhalconConfig;
use Phalcon\Filter;
use Phalcon\Http;
use Phalconeer\Condition;
use Phalconeer\RestRequest as This;

class RestRequest extends Http\Request
{
    const RESERVED_QUERY_PARAMETERS = [
        This\Helper\RestRequestHelper::PARAMETER_EMBED,
        This\Helper\RestRequestHelper::PARAMETER_FIELDS,
        This\Helper\RestRequestHelper::PARAMETER_LIMIT,
        This\Helper\RestRequestHelper::PARAMETER_OFFSET,
        This\Helper\RestRequestHelper::PARAMETER_SORT,
        This\Helper\RestRequestHelper::PARAMETER_URL,
    ];

    public function __construct(
        protected Filter\Filter $filter,
        protected PhalconConfig\Config $config
    )
    {
        $this->filter = $filter;
        $this->config = $config;
    }

    /**
     * Recursive function.
     * The function will detects the leafs from a multileveled query parameter in each iteration: <br />
     * competition,members{<b>player{id,name}</b>,<b>position{id}</b>},<b>boosts{id,name}</b> <br />
     * It collects the leafs into an array and replace them in the original string with a leaf-path:<br />
     * competition,members{<b>player.id</b>,<b>player.name</b>,<b>position.id</b>}...
     */
    protected function extractParameterTree(string $initialString) : array
    {
        if ($initialString === '') {
            return [];
        }

        $flattenedParameterList = preg_replace_callback('/(\w+){([,\.\w]*)}/', function($found) {
            return implode(',', array_map(function($subProperty) use($found) {
                        return $found[1] . '.' . $subProperty;
                    }, explode(',', $found[2])));
        }, $initialString);

        return (strpos($flattenedParameterList, '{') === false)
                ? explode(',', $flattenedParameterList)
                : $this->extractParameterTree($flattenedParameterList);
    }

    public function getEmbedList() : array
    {
        return $this->extractParameterTree(
            $this->getQuery(
                This\Helper\RestRequestHelper::PARAMETER_EMBED,
                'string',
                ''
            )
        );
    }

    public function getResponseMask() : array
    {
        return $this->extractParameterTree(
            $this->getQuery(
                This\Helper\RestRequestHelper::PARAMETER_FIELDS,
                'string',
                ''
            )
        );
    }

    public function getOffset(int $default = null) : int
    {
        return (int) $this->getQuery(
            This\Helper\RestRequestHelper::PARAMETER_OFFSET,
            'int',
            $default ?? $this->config->get('defaultOffset', 0)
        );
    }

    public function getLimit(int $default = null) : int
    {
        return (int) $this->getQuery(
            This\Helper\RestRequestHelper::PARAMETER_LIMIT,
            'int',
            $default ?? $this->config->get('defaultPageSize', 20)
        );
    }

    public function getSort(string $default = '') : string
    {
        return $this->getQuery(
            This\Helper\RestRequestHelper::PARAMETER_SORT,
            'string',
            $default
        );
    }

    public function getSortingParameters() : array
    {
        $queryParameter = $this->getSort();
        return $queryParameter !== ''
                ? explode(',', $queryParameter)
                : [];
    }

    /**
     * @todo Since php replace the dots in variable names we need to change it back here.
     * @link php.net/manual/en/language.variables.external.php See section "Dots in incoming variable names"
     */
    public function getQueryParameters() : array
    {
        $searchParameters = array_diff_key(
            $this->getQuery(),
            array_flip(self::RESERVED_QUERY_PARAMETERS)
        );

        return array_reduce(
            array_keys($searchParameters),
            function($normalizedSearchParameters, $currentKey) use ($searchParameters) {
                $normalizedKey = str_replace('_', '.', $currentKey);
                $normalizedSearchParameters[$normalizedKey] = $this->filter->sanitize(
                    $searchParameters[$currentKey],
                    'string'
                );
            return $normalizedSearchParameters;
        }, []);
    }

    public function parseDateRangeToCondition(
        string $field,
        \DateTime $minDate = null,
        \DateTime $maxDate = null
    ) : ?array
    {
        $dateAfter = $this->getQuery($field . 'After', 'string');
        $dateBefore = $this->getQuery($field . 'Before', 'string');
        if (is_null($dateAfter)
            && is_null($dateBefore)) {
            return null;
        }
        if (!is_null($dateAfter)) {
            $chunks = explode('.', $dateAfter);
            if (array_key_exists(1, $chunks)) {
                $chunks[1] = str_replace('-', ':', $chunks[1]);
            }
            $dateAfter = new \DateTime(implode(' ', $chunks));
            if (!is_null($minDate)) {
                $diff = $dateAfter->diff($minDate);
                if ($diff->invert === 0) {
                    $dateAfter = $minDate;
                }
            }
            if (!is_null($maxDate)) {
                $diff = $dateAfter->diff($maxDate);
                if ($diff->invert === 1) {
                    $dateAfter = $maxDate;
                }
            }
        } elseif (!is_null($minDate)
            && !is_null($dateBefore)) {
            $dateAfter = $minDate;
        }

        if (!is_null($dateBefore)) {
            $chunks = explode('.', $dateBefore);
            if (array_key_exists(1, $chunks)) {
                $chunks[1] = str_replace('-', ':', $chunks[1]);
            }
            $dateBefore = new \DateTime(implode(' ', $chunks));
            if (!is_null($minDate)) {
                $diff = $dateBefore->diff($minDate);
                if ($diff->invert === 0) {
                    $dateBefore = $minDate;
                }
            }
            if (!is_null($maxDate)) {
                $diff = $dateBefore->diff($maxDate);
                if ($diff->invert === 1) {
                    $dateBefore = $maxDate;
                }
            }
        } elseif (!is_null($maxDate)
            && !is_null($dateAfter)) {
            $dateBefore = $maxDate;
        }

        $operator = (is_null($dateAfter))
            ? Condition\Helper\ConditionHelper::OPERATOR_LESS_OR_EQUAL
            : ((is_null($dateBefore))
                ? Condition\Helper\ConditionHelper::OPERATOR_GREATER_OR_EQUAL
                : Condition\Helper\ConditionHelper::OPERATOR_BETWEEN);
        return [
            'value'         => ($operator === Condition\Helper\ConditionHelper::OPERATOR_BETWEEN)
                ? [$dateAfter->format('Y-m-d H:i:s'), $dateBefore->format('Y-m-d H:i:s')]
                : (is_null($dateAfter) ? $dateBefore->format('Y-m-d H:i:s') : $dateAfter->format('Y-m-d H:i:s')),
            'operator'      => $operator,
        ];
    }

    public function getSanitizedJsonBody(bool $associative = true, array $sanitizers = []) : array
    {
        $body = parent::getJsonRawBody($associative);
        if (!is_array($body)) {
            return [];
        }
        if (count($sanitizers) === 0) {
            $sanitizers = $this->config->get('defaultSanitizers', [Filter\Filter::FILTER_STRING]);
        }
        return $this->filter->sanitize($body, $sanitizers);
    }

    public function getToken() : string
    {
        return str_replace('Bearer ', '', $this->getHeader('Authorization'));
    }

    public function getPagerQueryParams(
        int $limit = null,
        int $offset = null,
        string $sort = '',
        array $queryParameters = []
    ) {
        if (!is_null($limit)
            && $limit !== $this->config->get('defaultPageSize', 20)) {
            $queryParameters['limit'] = $limit;
        }
        if (!is_null($offset)
            && $offset !== $this->config->get('defaultOffset', 0)) {
            $queryParameters['offset'] = $offset;
        }
        if ($sort !== '') {
            $queryParameters['sort'] = $sort;
        }

        return $queryParameters;
    }
}
