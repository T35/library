<?php

namespace t35\Library\Callback;

use JetBrains\PhpStorm\Pure;
use t35\Library\ListSimple;

/**
 * Класс-callback-функция.
 * Один аргумент.
 * Проверяет значение на наличие в списке.
 */
class CallbackValueInList {
    /**
     * @param ListSimple $list
     * @param $white "Белый", либо "черный" список.
     */
    public function __construct(
        protected ListSimple $list,
        protected bool       $white = true
    ) {
    }

    #[Pure] public function __invoke(mixed $value): bool {
        return
            $this->white
                ? $this->list->in_array($value)
                : !$this->list->in_array($value);
    }
}
