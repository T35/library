<?php

namespace t35\Library\Strings;

use Error;
use JetBrains\PhpStorm\Pure;
use t35\Library\BaseClass;
use t35\Library\FailedValue;
use t35\Library\IJSONSerializable;
use t35\Library\Arrays\ListString;
use t35\Library\Exceptions\stdException;
use TypeError;

class StringBase extends BaseClass implements IJSONSerializable {
    /** @var string Базовая строка */
    protected string $string;

    /**
     * @var StringBase Кодировка строки до применения конвертации, если та была произведена.
     * @see StringBase::ConverseToUTF8()
     */
    protected StringBase $InitEncoding;

    /**
     * Возвращает кодировку базовой строки до применения конвертации.
     * Если конвертация не применялась, вернет "false".
     *
     * @return StringBase|false
     */
    public function InitEncoding(): StringBase|false {
        return $this->InitEncoding ?? false;
    }

    /**
     * @var array Массив, содержащий список возможных кодировок для ТОЧНОГО(strict) определения кодировки при применении конвертации.
     * @see StringBase::ConverseToUTF8()
     */
    protected static array $encodings = [
        'UTF-8',
        'Windows-1251'
    ];

    /**
     * Переопределение списка кодировок для определения кодировки базовой строки в процессе конвертации в UTF-8.
     *
     * @param ListString $listEncodings
     * @return void
     * @see StringBase::ConverseToUTF8()
     */
    public static function SetEncodings(ListString $listEncodings): void {
        self::$encodings = $listEncodings->toArray();
    }

    /**
     * @param mixed $value
     * @param bool $converseToUTF8 Если "true", входная строка будет приведена к UTF-8, если это возможно.
     * @throws stdException
     */
    public function __construct(
        mixed $value = '',
        bool  $converseToUTF8 = false
    ) {
        $this->Set($value);
        if ($converseToUTF8)
            $this->ConverseToUTF8();
    }

    /**
     * Преобразование значения в объект StringBase static класса.
     * Например, параметры типа string вначале всех методов, для удобства, нужно преобразовать в объект StringBase static класса.
     *
     * @param mixed $value
     * @param bool $converseToUTF8
     * @return void
     * @throws stdException
     */
    public static function Converse(mixed &$value, bool $converseToUTF8 = false): void {
        if (!($value instanceof StringBase)) {
            $value = new static($value, $converseToUTF8);
        }
    }

    /**
     * Возвращает строку в формате string.
     *
     * @return string
     */
    protected function GetString(): string {
        return $this->string;
    }

    /**
     * Реализация приведения объекта к типу string.
     *
     * @return string
     */
    #[Pure] public function __toString(): string {
        return $this->GetString();
    }

    /**
     * Сеттер для строки.
     *
     * @param mixed $value
     * @return StringBase
     * @throws stdException
     */
    public function Set(mixed $value): static {
        try {
            $this->string = $value;
        }
        catch (TypeError $exception) {
            throw new stdException(
                $exception->getMessage(),
                $value,
                null,
                $exception->getCode()
            );
        }

        return $this;
    }

    /**
     * Добавляет текст слева.
     *
     * @param mixed $value
     * @return $this
     * @throws stdException
     */
    public function Prefix(mixed $value): static {
        try {
            $this->string = $value . $this->string;
        }
        catch (TypeError $exception) {
            throw new stdException(
                $exception->getMessage(),
                $value,
                null,
                $exception->getCode()
            );
        }

        return $this;
    }

    /**
     * Добавляет текст справа.
     *
     * @param mixed $value
     * @return $this
     * @throws stdException
     */
    public function Postfix(mixed $value): static {
        try {
            $this->string .= $value;
        }
        catch (TypeError $exception) {
            throw new stdException(
                $exception->getMessage(),
                $value,
                null,
                $exception->getCode()
            );
        }

        return $this;
    }

    /**
     * Реализация интерфейса.
     *
     * @return string
     * @see IJSONSerializable
     */
    #[Pure] public function JSONSerialize(): string {
        return $this->GetString();
    }

    /**
     * Приведение исходной строки к UTF-8.
     *
     * @return void
     * @throws stdException
     */
    public function ConverseToUTF8(): void {
        if ($encoding = mb_detect_encoding($this->string, static::$encodings, true)) {
            $this->InitEncoding = new StringBase($encoding);

            $this->string = mb_convert_encoding($this->string, 'UTF-8', $this->InitEncoding());
        }
        else {
            throw new stdException(
                'Не удалось определить кодировку строки',
                [
                    'encodings' => static::$encodings,
                    'string' => $this->string
                ]
            );
        }
    }

    /**
     * Возвращает строку в той кодировке, в которой была изначально при создании объекта.
     * Если изначально не проводилась перекодировка, то будет возвращена внутренняя строка.
     *
     * @return string
     */
    #[Pure] public function ConversedBack(): string {
        if (!$this->InitEncoding()) {
            return $this->string;
        }

        return mb_convert_encoding($this->string, $this->InitEncoding, 'UTF-8');
    }

    /**
     * Реализация file_get_contents с учетом отличной от UTF-8 кодировки у исходного файла.
     *
     * @param string $filename
     * @param bool $use_include_path
     * @param $context
     * @param int $offset
     * @param int|null $length
     * @return StringBase
     * @throws stdException
     * @see file_get_contents()
     */
    public static function file_get_contents(
        string $filename,
        bool   $use_include_path = false,
               $context = null,
        int    $offset = 0,
        int    $length = null
    ): StringBase {
        try {
            $string = file_get_contents(
                $filename,
                $use_include_path,
                $context,
                $offset,
                $length
            );
        }
        catch (Error $error) {
            throw new stdException(
                'Ошибка чтения файла: ' . $error->getMessage(),
                [
                    'filename' => $filename
                ],
                stdException::Conversed($error),
                $error->getCode()
            );
        }

        if ($string === false) {
            throw new stdException(
                'Не удалось загрузить файл',
                [
                    'filename' => $filename
                ]
            );
        }

        return new StringBase($string, true);
    }

    /**
     * Реализация file_put_contents с учетом отличной от UTF-8 кодировки у исходного файла.
     *
     * @param string $filename
     * @param bool $conversedBack Если true, в файл пойдет строка в кодировке, полученной при создании файла.
     * @param int $flag
     * @param null $context
     * @return int|false
     * @throws stdException
     * @see file_put_contents()
     */
    public function file_put_contents(
        string $filename,
        bool   $conversedBack = false,
        int    $flag = 0,
               $context = null
    ): int|false {
        try {
            return file_put_contents(
                $filename,
                $conversedBack
                    ? $this->ConversedBack()
                    : $this->string,
                $flag,
                $context
            );
        }
        catch (Error $error) {
            throw new stdException(
                'Ошибка записи файла: ' . $error->getMessage(),
                [
                    'filename' => $filename
                ],
                stdException::Conversed($error),
                $error->getCode()
            );
        }
    }

    /**
     * Реализация стандартного функционала.
     *
     * @return int
     * @see mb_strlen()
     */
    public function strlen(): int {
        return mb_strlen($this->string);
    }

    /**
     * Реализация стандартного функционала.
     *
     * @param StringBase $needle
     * @return int
     * @see mb_strpos()
     */
    public function strpos(StringBase $needle): int {
        return mb_strpos($this->string, $needle);
    }

    /**
     * Реализация стандартного функционала.
     *
     * @param StringBase $needle
     * @return int
     * @see mb_stripos()
     */
    public function stripos(StringBase $needle): int {
        return mb_stripos($this->string, $needle);
    }
}
