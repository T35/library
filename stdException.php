<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;
use Throwable;

class stdException extends \Exception {
    private mixed $value_with_error;

    public function View(
        $DevMode = true,
        $CutFilePath = false
    ) {
        if ($DevMode == true) {
            $throwable = $this;
            $message_num = 1;
            $message = '';
            while ($throwable !== null) {
                $path_info = pathinfo($throwable->getFile());

                $message .= '<br><b>MESSAGE ' . $message_num++ . '</b><br>';
                $message .= 'CODE: <b>' . $throwable->getCode() . '</b><br>';
                $message .= 'DIR: <b>' . ($CutFilePath ? str_replace($CutFilePath, '', $path_info['dirname']) : $path_info['dirname']) . '</b><br>';
                $message .= 'FILE: <b>' . $path_info['basename'] . '</b><br>';
                $message .= 'LINE: <b>' . $throwable->getLine() . '</b><br>';
                $message .= 'MESSAGE: <b>' . $throwable->getMessage() . '</b><br>';
                $message .= 'TRACE: ' . $throwable->getTraceAsString() . '<br>';
                $message .= $throwable->value_with_error !== null ? 'VALUE: <b>' . SimpleLibrary::PreVarDump($throwable->value_with_error, false) : '';
                $message .= '</b><br>';
                $message .= '<br>';

                $throwable = $throwable->getPrevious();
            }
        }
        else {
            $message = 'КАКАЯ-ТО ОШИБКА!';
        }

        SimpleLibrary::PreVarDump($message);
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
}
