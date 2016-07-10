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
 * Class WeKastAPIException
 * @package App\Exceptions
 */
class WeKastAPIException extends \Exception
{
    static $errors = [
        0 => 'Error',
        1 => 'Duplicate login',
        2 => 'Duplicate email',
        3 => 'Error email format',
        4 => 'Min 6 symbols in login',
        5 => 'User not found with this password',
    ];

    /**
     * WeKastAPIException constructor.
     * @param string $code
     * @param Exception|null $previous
     */
    public function __construct($code, Exception $previous = null)
    {
        $message = isset(self::$errors[$code]) ? self::$errors[$code] : self::$errors[0];
        parent::__construct($message, $code, $previous);
    }

    /**
     * GetMessage wrapper
     * @return string
     */
    public function message()
    {
        if (($this->getCode() === 0) && $this->getPrevious()) {
            return $this->getMessage() . ': ' . $this->getPrevious()->getMessage();
        } else {
            return $this->getMessage();
        }
    }


}