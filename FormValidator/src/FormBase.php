<?php
namespace Phalconeer\Module\FormValidator;

use Phalconeer\Exception;
use Phalconeer\Module\FormValidator;

abstract class FormBase
{
    /**
     * Datas for help validate creating resource requests
     *
     * @var array
     */
    protected $create = [];

    /**
     * Datas for help validate updating resource requests
     *
     * @var array
     */
    protected $update = [];

    /**
     * Datas for help validate deleting resource requests
     *
     * @var array
     */
    protected $delete = [];

    /**
     * Datas for help validate searching resource requests
     *
     * @var array
     */
    protected $search = [];

    /**
     * Returns with the field rules for the given operation.
     *
     * @param string $operation
     *
     * @return array
     *
     * @throws Exception
     */
    public function getFieldRules($operation)
    {
        if (!array_key_exists('fields', $this->$operation)) {
            throw new FormValidator\Exception\InvalidFormConfigurationException(get_class($this) . '::' . $operation);
        }
        switch ($operation) {
            case FormValidator\Helper\FormValidatorHelper::ACTION_SEARCH:
                return $this->search['fields'];
            case FormValidator\Helper\FormValidatorHelper::ACTION_CREATE:
                return $this->create['fields'];
            case FormValidator\Helper\FormValidatorHelper::ACTION_UPDATE:
                return $this->update['fields'];
            case FormValidator\Helper\FormValidatorHelper::ACTION_DELETE:
                return $this->delete['fields'];
            default:
                throw new Exception\InvalidArgumentException('Invalid field group');
        }
    }
}
