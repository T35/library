<?php

namespace t35\Library\Arrays;

use t35\Library\Arrays\ArrayBase;
use t35\Library\Arrays\ListUnique;
use t35\Library\EInclusionStatus;

/**
 * Реализация списка с флагом: обязательный, "белый" или "черный".
 */
class ListWithRequireStatus extends ListUnique {
    /**
     * Возвращает статус списка: обязательный, "белый" или "черный".
     *
     * @return EInclusionStatus
     */
    public function requireStatus(): EInclusionStatus {
        return $this->requireStatus;
    }

    public function __construct(
        ArrayBase|array            $value = null,
        protected EInclusionStatus $requireStatus = EInclusionStatus::WhiteList,
    ) {
        parent::__construct($value);
    }
}
