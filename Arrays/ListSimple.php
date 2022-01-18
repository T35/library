<?php

namespace t35\Library\Arrays;

use JetBrains\PhpStorm\Pure;
use t35\Library\Arrays\ArrayBase;

class ListSimple extends ArrayBase {
    public function offsetSet(mixed $offset, mixed $value): void {
        parent::offsetSet(null, $value);
    }
}
