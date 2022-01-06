<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;
use TypeError;

class StringBase implements IJSONSerializable {
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

    public function Get(): string {
        return $this->string;
    }

    #[Pure] public function __toString(): string {
        return $this->Get();
    }

    /**
     * Геттер поля $format.
     *
     * @return EStringFormat
     */
    public function format(): EStringFormat {
        return $this->format;
    }

    /**
     * Сеттер поля $format.
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

    #[Pure] public function JSONSerialize(): string {
        return $this->Get();
    }

    public function WithLineBreak(): string {
        return $this->Get() . $this->format()->LineBreak();
    }
}
