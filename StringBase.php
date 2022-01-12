<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;
use TypeError;

class StringBase extends BaseClass implements IJSONSerializable {
    protected string $string;

    /**
     * @param mixed $value
     * @param EStringFormat $format
     * @throws stdException
     */
    public function __construct(
        mixed $value = '',
        protected EStringFormat $format = EStringFormat::None
    ) {
        $this->Set($value);
    }

    /**
     * Преобразование значения в объект StringBase static класса.
     * Например, параметры типа string вначале всех методов, для удобства, нужно преобразовать в объект StringBase static класса.
     *
     * @param mixed $value
     * @param EStringFormat $format
     * @return void
     * @throws stdException
     */
    public static function Converse(mixed &$value, EStringFormat $format = EStringFormat::None): void {
        if (!($value instanceof StringBase)) {
            $value = new static($value, $format);
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
     * Возвращает объект формата.
     *
     * @return EStringFormat
     */
    public function format(): EStringFormat {
        return $this->format;
    }

    /**
     * Определение объекта формата.
     *
     * @param EStringFormat $format
     * @return StringBase
     */
    public function SetFormat(EStringFormat $format): static {
        $this->format = $format;
        return $this;
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
     * @see IJSONSerializable
     * @return string
     */
    #[Pure] public function JSONSerialize(): string {
        return $this->GetString();
    }
}
