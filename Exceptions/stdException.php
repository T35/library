<?php

namespace t35\Library\Exceptions;

use Exception;
use JetBrains\PhpStorm\Pure;
use t35\Library\Strings\EStringFormat;
use t35\Library\SimpleLibrary;
use t35\Library\Strings\StringBase;

class stdException extends Exception {
    private mixed $value_with_error;

    /**
     * @param bool $DevMode
     * @param bool $CutFilePath
     * @param \t35\Library\Strings\EStringFormat $format
     * @return void
     * @throws stdException
     */
    public function View(
        bool          $DevMode = true,
        bool          $CutFilePath = false,
        EStringFormat $format = EStringFormat::HTML
    ) {
        if ($DevMode == true) {
            $throwable = $this;
            $message_num = 1;
            $message = '';
            while ($throwable !== null) {
                $path_info = pathinfo($throwable->getFile());

                $message .= $format->LineBreak() . $format->Bold(new StringBase('MESSAGE ' . $message_num++)) . $format->LineBreak();
                $message .= 'CODE: ' . $format->Bold(new StringBase($throwable->getCode())) . $format->LineBreak();
                $message .= 'DIR: ' . $format->Bold(new StringBase($CutFilePath
                        ? str_replace($CutFilePath, '', $path_info['dirname'])
                        : $path_info['dirname'])) . $format->LineBreak();
                $message .= 'FILE: ' . $format->Bold(new StringBase($path_info['basename'])) . $format->LineBreak();
                $message .= 'LINE: ' . $format->Bold(new StringBase($throwable->getLine())) . $format->LineBreak();
                $message .= 'MESSAGE: ' . $format->Bold(new StringBase($throwable->getMessage())) . $format->LineBreak();
                $message .= 'TRACE: ' . $throwable->getTraceAsString() . $format->LineBreak();
                $message .= ($throwable->value_with_error !== null
                        ? 'VALUE: ' . $format->Bold(new StringBase(SimpleLibrary::GetVarDump($throwable->value_with_error, $format)))
                        : '') . $format->LineBreak();
                $message .= $format->LineBreak();

                $throwable = $throwable->getPrevious();
            }
        }
        else {
            $message = 'КАКАЯ-ТО ОШИБКА!';
        }

        echo SimpleLibrary::GetVarDump($message, $format);
    }

    #[Pure] public function __construct(
        $message = "",
        $value_with_error = null,
        stdException $previous = null,
        $code = 0
    ) {
        $this->value_with_error = $value_with_error;
        parent::__construct($message, $code, $previous);
    }

    #[Pure] public static function Conversed(Exception $exception): static {
        if (!($exception instanceof stdException))
            return new stdException(
                'Исключение класса "' . get_class($exception) . '": ' . $exception->getMessage(),
                null,
                null,
                $exception->getCode()
            );

        return $exception;
    }
}
