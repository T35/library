<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;

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

    public function Get(): string {
        return $this->string;
    }

    #[Pure] public function __toString(): string {
        return $this->Get();
    }

    /**
     * Возвращает реализацию класса.
     *
     * @param mixed $value
     * @return static
     * @throws stdException
     */
    public static function Inst(mixed $value): static {
        return new static($value);
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
        catch (\TypeError $exception) {
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
        catch (\TypeError $exception) {
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
        catch (\TypeError $exception) {
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
}
