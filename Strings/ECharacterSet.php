<?php

namespace t35\Library\Strings;

use t35\Library\Arrays\ArrayBase;
use t35\Library\Arrays\ListString;
use t35\Library\Exceptions\stdException;

enum ECharacterSet: string {
    case Password = 'abcdefghijklmnoprstuvxyzABCDEFGHIJKLMNOPRSTUVXYZ1234567890.,()[]!?&^%@*$<>/|+-{}`~';
    case PasswordSimple = 'abcdefghijklmnoprstuvxyzABCDEFGHIJKLMNOPRSTUVXYZ1234567890';
    case MD5 = 'abcdef1234567890';

    /**
     * Возвращает значение в виде массива-листа.
     *
     * @return ListString
     */
    public function toList(): ListString {
        return new ListString(explode('', $this->value));
    }

    /**
     * Возвращает значение.
     *
     * @return StringBase
     * @throws stdException
     * @see StringBase
     */
    public function value(): StringBase {
        return new StringBase($this->value);
    }
}
