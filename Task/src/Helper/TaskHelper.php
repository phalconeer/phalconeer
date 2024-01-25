<?php
namespace Phalconeer\Task\Helper;

use Phalconeer\Dto;
use Phalconeer\Task as This;
use Phalcon\Config;

class TaskHelper
{
    const CONFIG_HANDLER = 'handler';

    const TASK_UNIQUE_ID_LENGTH = 12;

    const STATUS_NEW = 'new';

    const STATUS_PROCESSING = 'processing';

    const STATUS_DONE = 'done';

    const STATUS_ERRORED = 'errored';

    const STATUS_FAILED = 'failed';

    const STATUS_CANCELLED = 'cancelled';

    public static function getServerDetails() : This\Data\TaskEnvironment
    {
        return This\Data\TaskEnvironment::fromArray([
            'productName'       => PRODUCT,
            'server'            => array_key_exists('SERVER_ADDR', $_SERVER)
                ? $_SERVER['SERVER_ADDR']
                : null //TODO: add CLI server addrress
        ]);
    }

    public static function createTaskExecution(
        string $taskName,
        Config\Config $config,
        Dto\ArrayObjectExporterInterface $detail = null,
        \ArrayObject $parameters = null
    ) : This\Data\TaskExecution
    {
        if (is_null($parameters)) {
            $parameters = new \ArrayObject();
        }
        /**
         * @var \Phalconeer\Module\Task\Dto\TaskParameters $detail
         */
        if (!is_null($detail)) {
            $parameterClass = $config->get('parameterClass');
            if (is_null($parameterClass)) {
                throw new This\Exception\ParameterClassNotConfiguredException($taskName, This\Helper\ExceptionHelper::TASK__PARAMETER_CLASS_NOT_SET);
            }
            if (!$detail instanceof $parameterClass) {
                throw new This\Exception\ParameterClassNotConfiguredException($taskName, This\Helper\ExceptionHelper::TASK__PARAMETER_CLASS_NOT_ALLOWED);
            }
            $parameters->offsetSet('detailClass', $parameterClass);
        }

        if (!$parameters->offsetExists('expectedRunTime')) {
            $parameters->offsetSet(
                'expectedRunTime',
                (new \DateTime())->setTimestamp(
                    time() + ($detail?->runDelay() ?? $config->get('repeatInterval', 0))
                )
            );
        }
        if (!$parameters->offsetExists('priortiy')) {
            $parameters->offsetSet(
                'priortiy',
                $config->get('priority', 0)
            );
        }

        $parameters->offsetSet('definedOn', self::getServerDetails());
        $parameters->offsetSet('detail', $detail);
        $parameters->offsetSet('task', $taskName);
        
        return new This\Data\TaskExecution($parameters);
    }
}
