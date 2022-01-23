<?php

namespace t35\Library\Arrays;

use JetBrains\PhpStorm\Pure;
use t35\Library\Arrays\ArrayBase;

class ListSimple extends ArrayBase {
    public function offsetSet(mixed $offset, mixed $value): void {
        parent::offsetSet(null, $value);
    }

    /**
     * Реализация.
     *
     * @return string|int|null
     * @see ArrayBase::randIndex()
     */
    public function randIndex(): string|int|null {
        return rand(0, $this->count() - 1);
    }
}
