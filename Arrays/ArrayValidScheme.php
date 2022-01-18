<?php

namespace t35\Library\Arrays;

use t35\Library\Arrays\ArrayBase;
use t35\Library\Arrays\ListCallables;
use t35\Library\Arrays\MapSimpleTyped;

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
