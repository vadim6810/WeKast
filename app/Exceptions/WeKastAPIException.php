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
    ];

    /**
     * WeKastAPIException constructor.
     * @param string $code
     * @param Exception|null $previous
     */
    public function __construct($code, Exception $previous = null)
    {
        if ($code && isset(self::$errors[$code])) {
            $message = self::$errors[$code];
        } else {
            $code = 10;
            $debug = env('APP_DEBUG', false);
            $message = self::$errors[10] . ($debug ? ': ' . $previous->getMessage() : '');
        }
        parent::__construct($message, $code, $previous);
    }

}