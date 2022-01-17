<?php

namespace t35\Library;

/**
 * Статусы обязательности.
 * Обязательность, Белый список и Черный список.
 */
enum ERequireStatus {
    case Require;
    case WhiteList;
    case BlackList;

    public function isPositive(): bool {
        return match ($this) {
            self::Require,
            self::WhiteList => true,
            self::BlackList => false
        };
    }
}
