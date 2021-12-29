<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;

/**
 * Класс-callback-функция.
 * Один аргумент.
 * Проверяет значение на наличие в списке.
 */
class CallbackValueInList {
    public function __construct(
        protected ListSimple $list
    ) {
    }

    #[Pure] public function __invoke(mixed $value): bool {
        return $this->list->in_array($value);
    }
}
