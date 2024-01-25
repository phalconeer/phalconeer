<?php
namespace Phalconeer\RouterCli\Bo;

use Phalcon\Cli;
use Phalconeer\RouterCli\Helper\RouterCliHelper as RCH;

class RouterCliBo extends Cli\Router
{
    public function getArguments() : array
    {
        $unnamedParamCount = 0;
        $filter = $this->getDI()->get('filter');
        $cliParameters = $_SERVER['argv'];
        return array_reduce(
            array_keys($cliParameters),
            function ($aggregator, $cliKey) use ($cliParameters, $filter, $unnamedParamCount) {
                switch ($cliKey) {
                    case 0: // this is the filename called
                        return $aggregator;
                    case 1:
                        $aggregator[RCH::PARAM_KEY_TASK] = $filter->sanitize($cliParameters[$cliKey], 'string');
                        return $aggregator;
                    case 2:
                        $aggregator[RCH::PARAM_KEY_ACTION] = $filter->sanitize($cliParameters[$cliKey], 'string');
                        return $aggregator;
                    default:
                        $paramPieces = explode('=', $cliParameters[$cliKey]);
                        if (array_key_exists(1, $paramPieces)) {
                            $aggregator[RCH::PARAM_KEY_PARAMS][$paramPieces[0]] = $filter->sanitize($paramPieces[1], 'string');
                        } else {
                            $aggregator[RCH::PARAM_KEY_PARAMS][RCH::UNNAMED_PARAM_PREFIX . $unnamedParamCount++] = $filter->sanitize($paramPieces[0], 'string');
                        }
                        return $aggregator;
                }
            },
            []
        );

    }
}