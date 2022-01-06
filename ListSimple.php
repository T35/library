<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;

class ListSimple extends ArrayBase {
    public function offsetSet(mixed $offset, mixed $value): void {
        parent::offsetSet(null, $value);
    }
}
