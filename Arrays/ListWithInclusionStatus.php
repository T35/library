<?php

namespace t35\Library\Arrays;

use t35\Library\EInclusionStatus;

/**
 * Реализация списка с флагом: обязательный, "белый" или "черный".
 */
class ListWithInclusionStatus extends ListUnique {
    /**
     * Возвращает статус списка: обязательный, "белый" или "черный".
     *
     * @return EInclusionStatus
     */
    public function requireStatus(): EInclusionStatus {
        return $this->inclusionStatus;
    }

    public function __construct(
        ArrayBase|array            $value = null,
        protected EInclusionStatus $inclusionStatus = EInclusionStatus::WhiteList,
    ) {
        parent::__construct($value);
    }
}
