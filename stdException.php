<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;
use Throwable;

class stdException extends \Exception {
    private mixed $value_with_error;

    /**
     * @param bool $DevMode
     * @param bool $CutFilePath
     * @param EStringFormat $format
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

                $message .= $format->LineBreak() . $format->Bold(StringBase::Inst('MESSAGE ' . $message_num++)) . $format->LineBreak();
                $message .= 'CODE: ' . $format->Bold(StringBase::Inst($throwable->getCode())) . $format->LineBreak();
                $message .= 'DIR: ' . $format->Bold(StringBase::Inst($CutFilePath
                        ? str_replace($CutFilePath, '', $path_info['dirname'])
                        : $path_info['dirname'])) . $format->LineBreak();
                $message .= 'FILE: ' . $format->Bold(StringBase::Inst($path_info['basename'])) . $format->LineBreak();
                $message .= 'LINE: ' . $format->Bold(StringBase::Inst($throwable->getLine())) . $format->LineBreak();
                $message .= 'MESSAGE: ' . $format->Bold(StringBase::Inst($throwable->getMessage())) . $format->LineBreak();
                $message .= 'TRACE: ' . $throwable->getTraceAsString() . $format->LineBreak();
                $message .= ($throwable->value_with_error !== null
                        ? 'VALUE: ' . $format->Bold(StringBase::Inst(SimpleLibrary::GetVarDump($throwable->value_with_error, $format)))
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
}
