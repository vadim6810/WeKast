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
        0 => 'Error',
        1 => 'Duplicate login',
        2 => 'Duplicate email',
        3 => 'Error email format',
        4 => 'Min 6 symbols in login',
        5 => 'User not found with this password',
        6 => 'Bad password',
        7 => 'Bad file',
        8 => 'Presentation not belongs this user',
        9 => 'Presentation not found',
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
            $debug = env('APP_DEBUG', false);
            $message = self::$errors[0] . ($debug ? ': ' . $previous->getMessage() : '');
        }
        parent::__construct($message, $code, $previous);
    }

}