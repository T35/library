<?php

namespace t35\Library\Structure;

use t35\Library\Arrays\ArrayBase;
use t35\Library\Arrays\MapSimple;
use t35\Library\EFailedValueType;
use t35\Library\EInclusionStatus;
use t35\Library\Exceptions\stdException;
use t35\Library\FailedValue;

class NodeElementFields extends NodeElement {

    /**
     * @param ArrayBase $newNodeData
     * @param mixed $nodeData
     * @param string $field_name
     * @return void
     * @throws stdException
     */
    public function GenerateFromData(ArrayBase &$newNodeData, mixed $nodeData, string $field_name): void {
        if (!\t35\Library\is_array($nodeData)) {
            throw new stdException(
                'Для узла массива передан не массив',
                ['node_data' => $nodeData, 'field_name' => $field_name]
            );
        }

        /** @var MapSimple $nodeData */
        MapSimple::Converse($nodeData);

        $failedValue = ($this->inclusionStatus == EInclusionStatus::Require)
            ? new FailedValue(null, EFailedValueType::Exception)
            : new FailedValue(null);

        if ($value = $nodeData->getValid($field_name, $this->listCallables, $failedValue)) {
            $newNodeData[$field_name] = ($this->nestedStructure) ? $this->nestedStructure->Instantiate($value) : $value;
        }
    }
}
