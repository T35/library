<?php

namespace t35\Library\Strings;

use t35\Library\Strings\StringBase;
use t35\Library\Exceptions\stdException;

enum EStringFormat {
    case None;
    case CMD;
    case HTML;

    /**
     * Жирный текст.
     *
     * @param StringBase $stringBase
     * @return StringBase
     * @throws stdException
     */
    public function Bold(StringBase $stringBase): StringBase {
        return match ($this) {
            self::None,
            self::CMD => $stringBase,
            self::HTML => new StringBase('<b>' . $stringBase . '</b>')
        };
    }

    /**
     * Перенос строки.
     *
     * @return StringBase
     */
    public function LineBreak(): StringBase {
        return match ($this) {
            self::None,
            self::CMD => new StringBase(PHP_EOL),
            self::HTML => new StringBase('<br>')
        };
    }
}
