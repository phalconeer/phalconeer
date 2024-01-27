<?php
namespace Phalconeer\TaskRegistry\Bo;

use Phalconeer\TaskRegistry as This;

class TaskRegistryBo
{
    protected This\Data\ListenerCollection $listeners;

    public function __construct(
    )
    {
        $this->listeners = new This\Data\ListenerCollection();
    }

    public function registerTask(This\Handler\HandlerBase $module, ?This\Data\ListenerConfig $config)
    {
        if (is_null($config)) {
            $config = new This\Data\ListenerConfig();
        }
        $this->listeners->offsetSet(
            $module->taskName(),
            new This\Data\Listener(new \ArrayObject([
                'module'        => $module,
                'config'        => $config,
            ]))
        );
    }

    public function getRegisteredTasks() : This\Data\ListenerCollection
    {
        return $this->listeners;
    }

    public function getRegisteredTask(string $taskName) : This\Data\Listener
    {
        return $this->listeners->offsetGet($taskName);
    }

    public function getModule(string $taskName) : ?This\TaskInterface
    {
        $module = $this->getRegisteredTask($taskName)
            ?->module();
        return ($module instanceof This\TaskInterface) ? $module : null;
    }

    public function getConfig(string $taskName) : ?This\Data\ListenerConfig
    {
        return $this->getRegisteredTask($taskName)?->config();
    }

    public function createTaskExecution(
        string $taskName,
        This\Data\TaskParameters $detail = null,
        \ArrayObject $parameters = null
    ) : This\Data\TaskExecution
    {


        if (is_null($parameters)) {
            $parameters = new \ArrayObject();
        }

        /**
         * @var This\Data\Listener $listener
         * @var This\Data\ListenerConfig $config
         */
        $listener = $this->listeners->offsetGet($taskName);
        $config = $listener->config();

        if (!is_null($detail)) {
            $parameterClass = $config->parameterClass();
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
                    time() + ($detail?->runDelay() ?? $config->repeatInterval())
                )
            );
        }
        if (!$parameters->offsetExists('priority')) {
            $parameters->offsetSet(
                'priortiy',
                $config->priority()
            );
        }

        $parameters->offsetSet('definedOn', This\Helper\TaskRegistryHelper::getServerDetails());
        $parameters->offsetSet('detail', $detail);
        $parameters->offsetSet('task', $taskName);
        
        return new This\Data\TaskExecution($parameters);
    }
}