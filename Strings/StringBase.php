<?php

namespace t35\Library\Strings;

use Error;
use JetBrains\PhpStorm\Pure;
use t35\Library\Arrays\ArrayBase;
use t35\Library\BaseClass;
use t35\Library\IJSONSerializable;
use t35\Library\Arrays\ListString;
use t35\Library\Exceptions\stdException;
use t35\Library\SimpleLibrary;
use TypeError;
use function mb_convert_encoding;
use function mb_detect_encoding;

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
     * Возвращает список кодировок.
     *
     * @param array|ArrayBase|null $addingEncodings Если передано, то в начало списка ставятся переданные кодировки.
     * @return ListString
     */
    public static function GetEncodings(array|ArrayBase $addingEncodings = null): ListString {
        $encodings = new ListString($addingEncodings);
        $encodings->putAll(self::$encodings);
        return $encodings;
    }

    /**
     * @param mixed $value
     * @param bool $convertToUTF8
     * @param ListString|null $listEncodings
     * @throws stdException
     * @see StringBase::SetEncodings()
     */
    public function __construct(
        mixed      $value = '',
        bool       $convertToUTF8 = false,
        ListString $listEncodings = null
    ) {
        $this->Set($value);
        if ($convertToUTF8) {
            $this->ConverseToUTF8($listEncodings);
        }
    }

    /**
     * Возвращает копию объекта с пустым контейнером(строкой).
     * Сохраняет все прочие настройки.
     *
     * @return $this
     * @throws stdException
     */
    public function similar(
        mixed $value = ''
    ): static {
        $new = new static($value);
        if ($this->InitEncoding()) {
            $new->InitEncoding = $this->InitEncoding();
        }
        return $new;
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
            $value = new static($value);
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
     * @param ListString|null $listEncodings
     * @return void
     * @throws stdException
     */
    public function ConverseToUTF8(ListString $listEncodings = null): void {
        if ($encoding = mb_detect_encoding($this->string, self::GetEncodings($listEncodings)->toArray(), true)) {
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
     * @param bool $convertToUTF8
     * @param ListString|null $listEncodings
     * @param bool $use_include_path
     * @param null $context
     * @param int $offset
     * @param int|null $length
     * @return StringBase
     * @throws stdException
     * @see file_get_contents()
     */
    public static function file_get_contents(
        string     $filename,
        bool       $convertToUTF8 = true,
        ListString $listEncodings = null,
        bool       $use_include_path = false,
                   $context = null,
        int        $offset = 0,
        int        $length = null
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

        return new StringBase($string, $convertToUTF8, $listEncodings);
    }

    /**
     * Реализация file_put_contents с учетом отличной от UTF-8 кодировки у исходного файла.
     *
     * @param string $filename
     * @param bool $conversedBack Если true, в файл пойдет строка в кодировке, определенной при создании файла.
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
     * @return int|false
     * @see mb_strpos()
     */
    public function strpos(StringBase $needle): int|false {
        return mb_strpos($this->string, $needle);
    }

    /**
     * Реализация стандартного функционала.
     *
     * @param StringBase $needle
     * @return int|false
     * @see mb_stripos()
     */
    public function stripos(StringBase $needle): int|false {
        return mb_stripos($this->string, $needle);
    }

    /**
     * Реализация стандартного функционала.
     *
     * @param int $start
     * @param int|null $length
     * @return static
     * @throws stdException
     */
    public function substr(
        int  $start,
        ?int $length = null
    ): static {
        return $this->similar(
            mb_substr(
                $this->string,
                $start,
                $length
            )
        );
    }

    /**
     * Вставляет подстроку на позицию в базовой строке.
     *
     * @param string|StringBase $insert
     * @param int $offset
     * @return void
     */
    public function substr_insert(
        string|StringBase $insert,
        int               $offset
    ): void {
        $prefixString = mb_substr($this->string, 0, $offset);
        $postfixString = mb_substr($this->string, $offset);
        $this->string = $prefixString . $insert . $postfixString;
    }

    /**
     * Определяет, является ли строка md5-хешем.
     *
     * @param string|StringBase $string
     * @param bool $case_sensitive
     * @return bool
     */
    public static function stringIsMD5(string|StringBase $string, bool $case_sensitive = false): bool {
        $pattern = $case_sensitive ? '/^[a-f0-9]{32}$/' : '/^[a-f0-9]{32}$/i';
        return preg_match($pattern, $string);
    }

    /**
     * Является ли строка md5-хешем.
     *
     * @param bool $case_sensitive
     * @return bool
     */
    public function isMD5(bool $case_sensitive = false): bool {
        return self::stringIsMD5($this->string, $case_sensitive);
    }

    /**
     * Генерирует пароль по указанному набору символов.
     *
     * @param ECharacterSet $characterSet
     * @param int $length
     * @return StringBase
     * @throws stdException
     */
    public static function GeneratePassword(
        ECharacterSet $characterSet,
        int           $length
    ): StringBase {
        $password = new StringBase();
        for ($i = 0; $i < $length; $i++) {
            $password->Postfix($characterSet->toList()->randValue());
        }

        return $password;
    }
}
