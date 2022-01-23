<?php

namespace t35\Library\Arrays;

use t35\Library\EInclusionStatus;

/**
 * Схема для проверки значений массива.
 * @see ListCallables
 */
class ArrayValidScheme extends MapSimpleTyped {
    public function inclusionStatus(): EInclusionStatus {
        return $this->inclusionStatus;
    }

    /**
     * @param ArrayBase|array|null $value
     * @param EInclusionStatus $inclusionStatus Если "BlackList", то этот список эквивалентен ListWithInclusionStatus с параметром "BlackList".
     */
    public function __construct(
        ArrayBase|array $value = null,
        protected EInclusionStatus $inclusionStatus = EInclusionStatus::WhiteList
    ) {
        parent::__construct(ListCallables::class, $value);
    }

    public function similar(ArrayBase|array $value = null): static {
        return new static($value, $this->inclusionStatus());
    }
}
