<?php
namespace Phalconeer\RouterCli\Helper;

class RouterCliHelper
{
    /**
     * This key is used to determine the task.
     */
    const PARAM_KEY_TASK = 'task';

    /**
     * This key is used to determine to action within the task.
     */
    const PARAM_KEY_ACTION = 'action';

    /**
     * Holds all the additionally passed variables.
     */
    const PARAM_KEY_PARAMS = 'params';

    /**
     * Unnamed cli parameters receive this prefix.
     */
    const UNNAMED_PARAM_PREFIX = 'param_';
}