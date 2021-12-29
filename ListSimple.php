<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;

class ListSimple extends ArrayBase {
    /**
     * Реализация $box с измененным PHPDoc.
     *
     * @var array<int, mixed>
     */
    protected array $box = [];

    public function offsetSet(mixed $offset, mixed $value): void {
        parent::offsetSet(null, $value);
    }

    #[Pure] public function last(): mixed {
        return $this->box[$this->count() - 1];
    }
}
