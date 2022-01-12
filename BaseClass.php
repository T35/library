<?php

namespace t35\Library;

class BaseClass {
    /**
     * Возвращает имя класса в виде класса StringBase.
     *
     * @see StringBase
     * @return StringBase
     */
    public static function class(): StringBase {
        return new StringBase(static::class);
    }
}
