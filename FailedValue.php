<?php

namespace t35\Library;

use Exception;

class FailedValue extends BaseClass {
    public function __construct(
        protected mixed            $value = false,
        protected EFailedValueType $failedValueType = EFailedValueType::Value
    ) {

    }

    public function value(): mixed {
        return $this->value;
    }

    /**
     * @param Exception|null $exception
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
                elseif ($exception !== null) {
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
