<?php

namespace t35\Library\Callback;

use JetBrains\PhpStorm\Pure;
use t35\Library\Arrays\ListWithInclusionStatus;

/**
 * Класс-callback-функция.
 * Один аргумент.
 * Проверяет значение на наличие в списке.
 */
class CallbackValueInList {
    /**
     * @param ListWithInclusionStatus $list
     */
    public function __construct(
        protected ListWithInclusionStatus $list,
    ) {
    }

    #[Pure] public function __invoke(mixed $value): bool {
        return
            $this->list->requireStatus()->isPositive()
                ? $this->list->in_array($value)
                : !$this->list->in_array($value);
    }
}
