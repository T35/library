<?php

namespace t35\Library;

use Exception;

/**
 * Результат ошибки. Чтобы можно было либо вернуть нужное значение, либо выбросить нужное исключение.
 */
class FailedValue extends BaseClass {
    /**
     * Если тип результата ошибки "значение"(Value), то возвращаться будет переданное значение.
     * Если тип результат "исключение"(Exception), то возвращаться будет либо переданное исключение(если таковое передано в качестве значения результата ошибки), либо стандартное исключение.
     *
     * @param mixed $value Значение результата ошибки.
     * @param EFailedValueType $failedValueType Тип результата ошибки.
     */
    public function __construct(
        protected mixed            $value = false,
        protected EFailedValueType $failedValueType = EFailedValueType::Value
    ) {

    }

    public function value(): mixed {
        return $this->value;
    }

    /**
     * @param Exception|null $exception Если тип результата ошибки "исключение", то это исключение менее приоритетно, чем переданное при создании.
     * @return mixed
     * @throws Exceptions\FailedValue
     * @throws Exception
     */
    public function Get(Exception $exception = null): mixed {
        switch ($this->failedValueType) {
            case EFailedValueType::Exception:
                if ($this->value instanceof Exception) {
                    throw $this->value;
                }
                if ($exception !== null) {
                    throw $exception;
                }
                throw new Exceptions\FailedValue();

            case EFailedValueType::Value:
                return $this->value;

            default:
                return null;
        }
    }
}
