<?php

namespace t35\Library\Arrays;

use t35\Library\Arrays\ListSimple;

/**
 * Лист только с уникальными значениями.
 */
class ListUnique extends ListSimple {
    public function offsetSet(mixed $offset, mixed $value): void {
        if (!$this->in_array($value))
            parent::offsetSet($offset, $value);
    }
}
