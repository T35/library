<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;
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
        'Windows-1251',
        'ISO-8859-1',
        'ASCII'
    ];

    /**
     * Переопределение списка кодировок для определения кодировки базовой строки в процессе конвертации в UTF-8.
     *
     * @param ListString $listEncodings
     * @return void
     *@see StringBase::ConverseToUTF8()
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
}
