<?php

namespace t35\Library\Structure;

use t35\Library\Arrays\ArrayBase;
use t35\Library\EFailedValueType;
use t35\Library\EInclusionStatus;
use t35\Library\FailedValue;
use t35\Library\ValidatingMethods;
use t35\Library\Exceptions;

class NodeElementValue extends NodeElement {

    /**
     * @param ArrayBase $newNodeData
     * @param mixed $nodeData
     * @param string $field_name
     * @return void
     * @throws Exceptions\FailedValue
     */
    public function GenerateFromData(ArrayBase &$newNodeData, mixed $nodeData, string $field_name): void {
        $failedValue = ($this->inclusionStatus == EInclusionStatus::Require)
            ? new FailedValue(null, EFailedValueType::Exception)
            : new FailedValue(null);
        if ($value = ValidatingMethods::Validated($nodeData, $this->listCallables, $failedValue))
            $newNodeData = ($this->nestedStructure) ? $this->nestedStructure->Instantiate($value) : $value;
    }
}
