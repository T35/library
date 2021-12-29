<?php

namespace t35\Library;

/**
 * Класс-callback-функция.
 * Один аргумент.
 * Проверяет значение на соответствие регулярному выражению.
 */
class CallbackRegExp {
    public function __construct(
        protected string $pattern
    ) {
    }

    public function __invoke($value): bool {
        return preg_match($this->pattern, $value);
    }
}
