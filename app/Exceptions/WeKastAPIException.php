<?php
/**
 * Created by IntelliJ IDEA.
 * User: Samanta
 * Date: 09.07.2016
 * Time: 23:50
 */

namespace App\Exceptions;

use Exception;

/**
 * Ошибки API
 *
 * Class WeKastAPIException
 * @package App\Exceptions
 */
class WeKastAPIException extends \Exception
{
    /**
     * Список ошибок API
     *
     * @var array
     */
    static $errors = [
        0 => 'No error',
        1 => 'Duplicate login',
        2 => 'Duplicate email',
        3 => 'Error email format',
        4 => 'Login must contains from 7 to 15 digits',
        5 => 'User not found with this password',
        6 => 'Bad password',
        7 => 'Bad file',
        8 => 'Presentation not belongs this user',
        9 => 'Presentation not found',
        10 => 'Unknown error',
        11 => 'Internal storage error',
        12 => 'User not found with this email',
        13 => 'Phone number not confirmed',
        14 => 'Phone number not found',
        15 => 'Bad code',
        16 => 'Phone already confirmed',
        17 => 'Invalid extension',
        18 => 'File not a zip archive',
        19 => 'Archive not contain info file',
        20 => 'Info file not a valid XML',
        21 => 'Preview not found',
    ];

    /**
     * WeKastAPIException constructor.
     * @param string $code
     * @param Exception|null $previous
     */
    public function __construct($code, Exception $previous = null)
    {
        $debug = env('APP_DEBUG', false);
        if ($code && isset(self::$errors[$code])) {
            $message = self::$errors[$code];
            if ($debug && !is_null($previous)) {
                $message .= ': ' . $previous->getMessage();
            }
        } else {
            $code = 10;
            $message = self::$errors[10] . ($debug ? ': ' . $previous->getMessage() : '');
        }
        parent::__construct($message, $code, $previous);
    }

}