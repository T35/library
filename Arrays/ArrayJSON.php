<?php

namespace t35\Library\Arrays;

use Exception;
use t35\Library\Exceptions;
use t35\Library\Strings\StringBase;

class ArrayJSON extends ArrayBase {
    /**
     * Реализация стандартного file_get_contents с использованием StringBase.
     *
     * @param string $filename
     * @param bool $convertToUTF8
     * @param ListString|null $listEncodings
     * @param bool $use_include_path
     * @param $context
     * @param int $offset
     * @param int|null $length
     * @return static
     * @throws Exceptions\stdException
     * @see file_get_contents()
     * @see StringBase::file_get_contents()
     */
    public static function file_get_contents(
        string     $filename,
        bool       $convertToUTF8 = true,
        ListString $listEncodings = null,
        bool       $use_include_path = false,
                   $context = null,
        int        $offset = 0,
        int        $length = null
    ): static {
        try {
            $string = StringBase::file_get_contents(
                $filename,
                $convertToUTF8,
                $listEncodings,
                $use_include_path,
                $context,
                $offset,
                $length
            );

            return new static(json_decode($string, true, 512, JSON_THROW_ON_ERROR));
        }
        catch (Exception $exception) {
            throw new Exceptions\stdException(
                'Не удалось загрузить объект класса "' . static::class() . '"',
                $string ?? null,
                Exceptions\stdException::Conversed($exception)
            );
        }
    }

    /**
     * Реализация стандартного file_put_contents с использованием StringBase.
     *
     * @param string $filename
     * @param int $json_flags
     * @param bool $conversedBack
     * @param int $flag
     * @param $context
     * @return int|false
     * @throws Exceptions\stdException
     * @see file_put_contents()
     * @see StringBase::file_put_contents()
     */
    public function file_put_contents(
        string $filename,
        int    $json_flags = JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        bool   $conversedBack = false,
        int    $flag = 0,
               $context = null
    ): int|false {
        try {
            return (new StringBase($this->toJSON($json_flags)))->file_put_contents(
                $filename,
                $conversedBack,
                $flag,
                $context
            );
        }
        catch (Exception $exception) {
            throw new Exceptions\stdException(
                'Не удалось записать JSON в файл',
                [
                    'filename'=>$filename,
                    'array'=>$this->toArray()
                ],
                Exceptions\stdException::Conversed($exception)
            );
        }
    }
}
