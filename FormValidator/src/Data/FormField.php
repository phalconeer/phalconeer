<?php
namespace Phalconeer\FormValidator\Data;

use Phalconeer\Dto;
use Phalconeer\FormValidator as This;

class FormField extends Dto\ImmutableCollectionDto
{
    use Dto\Trait\ArrayLoader;

    protected string $collectionType = This\Data\FieldCheck::class;

    protected function getCheckInstance(string $type, array $data) : This\Data\FieldCheck
    {
        switch ($type) {
            case This\Helper\FormValidatorHelper::CHECK_MAX_LENGTH:
                return This\Data\MaxLength::fromArray($data);
            case This\Helper\FormValidatorHelper::CHECK_MAX_VALUE:
                return This\Data\MaxValue::fromArray($data);
            case This\Helper\FormValidatorHelper::CHECK_MIN_LENGTH:
                return This\Data\MinLength::fromArray($data);
            case This\Helper\FormValidatorHelper::CHECK_MIN_VALUE:
                return This\Data\MinValue::fromArray($data);
            case This\Helper\FormValidatorHelper::CHECK_POSSIBLE_VALUES:
                return This\Data\PossibleValues::fromArray($data);
            case This\Helper\FormValidatorHelper::CHECK_REGEXP:
                return This\Data\Regex::fromArray($data);
            case This\Helper\FormValidatorHelper::CHECK_REQUIRED:
                return This\Data\Required::fromArray($data);
            case This\Helper\FormValidatorHelper::CHECK_TYPE:
                return This\Data\Type::fromArray($data);
        }
        throw new This\Exception\InvalidFormConfigurationException(
            'Invalid check type: ` ' . $type . ' `',
            This\Helper\ExceptionHelper::FORM__INVALID_CHECK_TYPE
        );
    }

    public function __construct(\ArrayObject $dataObject = null)
    {
        $this->collection = new \ArrayObject();

        $iterator = $dataObject->getIterator();
        while ($iterator->valid()) {
            $this->collection->offsetSet(
                $iterator->key(),
                $this->getCheckInstance($iterator->key(), $iterator->current())
            );
            $iterator->next();
        }
    }
}