<?php

namespace t35\Library;

use InvalidArgumentException;

/**
 * Схема для проверки значений массива.
 * @see ListCallables
 */
class ArrayValidScheme extends MapSimpleTyped {
    public function isStrict(): bool {
        return $this->strict;
    }

    public function __construct(
        ArrayBase|array $value = null,
        protected bool  $strict = false
    ) {
        parent::__construct(ListCallables::class, $value);
    }
}
