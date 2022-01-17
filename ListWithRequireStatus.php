<?php

namespace t35\Library;

/**
 * Реализация списка с флагом: обязательный, "белый" или "черный".
 */
class ListWithRequireStatus extends ListUnique {
    /**
     * Возвращает статус списка: обязательный, "белый" или "черный".
     *
     * @return ERequireStatus
     */
    public function requireStatus(): ERequireStatus {
        return $this->requireStatus;
    }

    public function __construct(
        ArrayBase|array          $value = null,
        protected ERequireStatus $requireStatus = ERequireStatus::WhiteList,
    ) {
        parent::__construct($value);
    }
}
