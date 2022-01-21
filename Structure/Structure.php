<?php

namespace t35\Library\Structure;

use Exception;
use t35\Library\Arrays\ArrayBase;
use t35\Library\Arrays\ListCallables;
use t35\Library\Arrays\ListSimple;
use t35\Library\Arrays\MapSimple;
use t35\Library\Arrays\MapSimpleTyped;
use t35\Library\EFailedValueType;
use t35\Library\EInclusionStatus;
use t35\Library\Exceptions;
use t35\Library\FailedValue;
use t35\Library\SimpleLibrary;
use t35\Library\Strings\StringBase;
use t35\Library\ValidatingMethods;
use function t35\Library\is_array;

/**
 * Объект структуры, описывающий способ инициализации данных.
 */
class Structure extends MapSimple {
    public const ClassName = '__className';
    public const ClassConstructorArgs = '__classConstructorArgs';
    public const NodeAsFields = '__nodeIsFields';
    public const NodeAsArray = '__nodeIsArray';
    public const NodeAsValue = '__nodeIsValue';
    public const NodeElementInclusionStatus = '__fieldInclusionStatus';
    public const NodeElementValidate = '__fieldValidate';
    public const NodeElementIsNestedStructure = '__fieldNestedStructure';
    public const NodeElements = '__nodeElements';
    public const NodeData = '__nodeData';

    /**
     * Имя класса, объект которого должен быть получен на выходе из входных данных.
     *
     * @return  StringBase
     */
    public function className(): StringBase {
        return $this->getSafe(self::ClassName);
    }

    /**
     * Список параметров для конструктора класса. Необязательный параметр.
     *
     * @return  ListSimple
     */
    public function classConstructorArgs(): ListSimple {
        return $this->getSafe(self::ClassConstructorArgs);
    }

    /**
     * Поля структуры. Каждое поле должно представлять собой массив со следующими полями:
     *  required - обязательно ли это поле. По-умолчанию - false;
     *
     * @return  MapSimpleTyped<NodeElement>
     */
    public function nodeElements(): MapSimpleTyped {
        return $this->getSafe(self::NodeElements);
    }

    /**
     * @param ArrayBase|array|null $value
     */
    public function __construct(ArrayBase|array $value = null) {
        $this->box[self::NodeElements] = new MapSimpleTyped(NodeElement::class);

        parent::__construct($value);
    }

    /**
     * Переопределение.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @throws Exceptions\stdException
     * @see MapSimple
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        try {
            switch ($offset) {
                case self::ClassName:
                    $value = ValidatingMethods::Validated(
                        $value,
                        new ListCallables(ValidatingMethods::VM_CLASS_EXISTS),
                        new FailedValue(false, EFailedValueType::Exception)
                    );

                    break;

                case self::ClassConstructorArgs:
                    $value = new ListSimple(ValidatingMethods::Validated(
                        $value,
                        new ListCallables(ValidatingMethods::VM_NOT_EMPTY_ARRAY),
                        new FailedValue([])
                    ));

                    break;

                case self::NodeAsFields:
                    $value = new MapSimple(ValidatingMethods::Validated(
                        $value,
                        new ListCallables(ValidatingMethods::VM_IS_ASSOC_OR_EMPTY_ARRAY),
                        new FailedValue(null, EFailedValueType::Exception)
                    ));

                    foreach ($value as $field_name => $item) {
                        $this->offsetGet(self::NodeElements)[$field_name] = $this->NodeElementArrayToObject($item, ENodeType::Fields);
                    }

                    break;

                case self::NodeAsArray:
                    $this->offsetGet(self::NodeElements)[$offset] = $this->NodeElementArrayToObject($value, ENodeType::Array);
                    break;

                case self::NodeAsValue:
                $this->offsetGet(self::NodeElements)[$offset] = $this->NodeElementArrayToObject($value, ENodeType::Value);
                    break;

                default:
                    return;

            }

            parent::offsetSet($offset, $value);
        }
        catch (Exceptions\FailedValue $exception) {
            throw new Exceptions\stdException(
                'Переданный массив не может быть структурой',
                ['offset' => $offset, 'value' => $value],
                $exception
            );
        }
    }

    /**
     * Возвращает объект элемента узла структуры, созданный из описания(части массива, описывающего структуру).
     *
     * @param array|ArrayBase $value
     * @param ENodeType $nodeType
     * @return NodeElement
     * @throws Exceptions\stdException
     */
    protected function NodeElementArrayToObject(array|ArrayBase $value, ENodeType $nodeType): NodeElement {
        MapSimple::Converse($value);

        return NodeElement::Instantiate(
            $nodeType,
            is_string($field_value = $value->getValid(
                self::NodeElementInclusionStatus,
                new ListCallables([function ($value) {
                    if (is_string($value))
                        return EInclusionStatus::tryFrom($value) ?? false;
                    else
                        return in_array($value, EInclusionStatus::cases());
                }]),
                new FailedValue(EInclusionStatus::WhiteList)
            )) ? EInclusionStatus::from($field_value) : $field_value,
            ($field_value = $value->getValid(
                self::NodeElementValidate,
                new ListCallables(ValidatingMethods::VM_IS_CALLABLE_LIST),
                new FailedValue()
            )) ? new ListCallables($field_value) : new ListCallables(),
            ($field_value = $value->getValid(
                self::NodeElementIsNestedStructure,
                new ListCallables(ValidatingMethods::VM_IS_ASSOC),
                new FailedValue()
            )) ? new Structure($field_value) : null
        );
    }

    /**
     * Реализация структуры из массива данных.
     *
     * @param mixed $data
     * @return mixed
     */
    public function Instantiate(mixed $data): mixed {
        $newNodeData = new ArrayBase();
        /** @var NodeElement $structureNodeElement */
        foreach ($this->offsetGet(self::NodeElements) as $field_name => $structureNodeElement) {
            $structureNodeElement->GenerateFromData($newNodeData, $data, $field_name);
        }

        $args = [];
        foreach ($this->offsetGet(self::ClassConstructorArgs) as $arg) {
            $args[] = $arg == self::NodeData ? $newNodeData->toArray() : $arg;
        }
        return new ($this->offsetGet(self::ClassName))(...$args);
    }
}
