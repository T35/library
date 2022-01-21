<?php

namespace t35\Library;

/**
 * Статусы включенности.
 * Обязательность, Белый список и Черный список.
 */
enum EInclusionStatus: string {
    case Require = '__IS_Require';
    case WhiteList = '__IS_WhiteList';
    case BlackList = '__IS_BlackList';

    /**
     * Позитивный или негативный тип включенности.
     *
     * @return bool
     */
    public function isPositive(): bool {
        return match ($this) {
            self::Require,
            self::WhiteList => true,
            self::BlackList => false
        };
    }
}
