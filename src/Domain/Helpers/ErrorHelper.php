<?php

namespace ZnLib\Rpc\Domain\Helpers;

class ErrorHelper
{

    public static function descriptionFromJsonErrorCode(int $jsonErrorCode): string {
        switch ($jsonErrorCode) {
            case JSON_ERROR_NONE: // Ошибок нет
                $errorDescription = 'No errors';
                break;
            case JSON_ERROR_DEPTH: // Достигнута максимальная глубина стека
                $errorDescription = 'Maximum stack depth reached';
                break;
            case JSON_ERROR_STATE_MISMATCH: // Некорректные разряды или несоответствие режимов
                $errorDescription = 'Incorrect digits or mode mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR: // Некорректный управляющий символ
                $errorDescription = 'Invalid control character';
                break;
            case JSON_ERROR_SYNTAX: // Синтаксическая ошибка, некорректный JSON
                $errorDescription = 'Syntax error, invalid JSON';
                break;
            case JSON_ERROR_UTF8: // Некорректные символы UTF-8, возможно неверно закодирован
                $errorDescription = 'Incorrect UTF-8 characters, possibly incorrectly encoded';
                break;
            default: // Неизвестная ошибка
                $errorDescription = 'Unknown error';
                break;
        }
        return $errorDescription;
    }
}
