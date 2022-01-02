<?php

namespace t35\Library;

class StringBase {
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
    }

    public function __toString(): string {
        return $this->string;
    }

    /**
     * Возвращает реализацию класса.
     *
     * @param mixed $value
     * @return static
     * @throws stdException
     */
    public static function Inst(mixed $value): static {
        return new StringBase($value);
    }

    /**
     * Добавляет текст слева.
     *
     * @param StringBase $stringBase
     * @return $this
     */
    public function Prefix(StringBase $stringBase): static {
        $this->string = $stringBase . $this->string;
        return $this;
    }

    /**
     * Добавляет текст справа.
     *
     * @param StringBase $stringBase
     * @return $this
     */
    public function Postfix(StringBase $stringBase): static {
        $this->string .= $stringBase;
        return $this;
    }
}
