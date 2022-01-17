<?php

namespace t35\Library;

/**
 * Объект структуры, описывающий способ инициализации данных.
 */
class Structure extends BaseClass {
    /**
     * @var class-string<IConstructableByStructure> Имя класса, объект которого должен быть получен на выходе из входных данных.
     */
    protected string $structureClass;

    /**
     * Поля структуры. Каждое поле должно представлять собой массив со следующими полями:
     *  required - обязательно ли это поле. По-умолчанию - false;
     *
     * @var MapSimple
     */
    protected MapSimple $fields;

    /**
     * @param array|ArrayBase $array
     * @throws stdException
     */
    public function __construct(array|ArrayBase $array) {
        ArrayBase::Converse($array);

        $this->structureClass = $array->getValid(
            'structureClass',
            [static::class, 'isConstructableByStructure']
        );

        $this->fields = new MapSimple($array->getValid(
            'fields',
            ValidatingMethods::VM_NOT_EMPTY_ARRAY
        ));

        foreach ($this->fields as $field_name=>&$field) {
            if (!\is_array($field)) {
                throw new stdException(
                    'Элемент массива "fields" структуры(Structure) должен быть массивом. Передан "' . get_debug_type($field) . '"',
                    $field
                );
            }

            /** @var ArrayBase $field */
            ArrayBase::Converse($field);

            if ($field->getSafe('nestedStructure')) {
                $field['nestedStructure'] = new Structure($field['nestedStructure']);
            }
        }
    }

    /**
     * Возвращает объект созданный на основе структуры объект.
     *
     * @param array|ArrayBase $input_data
     * @return IConstructableByStructure
     * @throws stdException
     */
    public function GetInstantiate(array|ArrayBase $input_data): IConstructableByStructure {
        /** @var ArrayBase $input_data */
        ArrayBase::Converse($input_data);

        $output_data = new ArrayBase();

        /** @var ArrayBase $field */
        foreach ($this->fields as $field_name=>$field) {
            if ($field_name === '__array') {
                foreach ($input_data as $item) {
                    $output_data[] = ValidatingMethods::Validated(
                        $item,
                        ($field->array_key_exists('VM') ? new ArrayBase($field['VM']) : null)
                    );
                }

                break;
            }

            if ($output_value = $field->getValid(
                $field_name,
                ($field->array_key_exists('VM') ? new ArrayBase($field['VM']) : null),
                $field->getSafe('required', false)
            )) {
                $output_data[($field_name === '__array') ? null : $field_name] = $output_value;
            }
        }

        return $this->structureClass::InstByStructure($this);
    }
}
