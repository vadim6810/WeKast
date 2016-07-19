<?php
/**
 * Created by IntelliJ IDEA.
 * User: Samanta
 * Date: 19.07.2016
 * Time: 15:33
 */

namespace App\Exceptions;


use Illuminate\Database\QueryException;

class WeKastDuplicateException extends WeKastAPIException
{
    public function __construct(QueryException $e)
    {
        switch ($e->errorInfo[0]) {
            case '23000':
                $m = array();
                if (preg_match('#(login|email)(?:_unique\'|)$#', $e->errorInfo[2], $m)) {
                    $errors = [
                        'login' => 1,
                        'email' => 2,
                    ];
                    parent::__construct(isset($errors[$m[1]]) ? $errors[$m[1]] : 0, $e);
                    break;
                }
            default:
                parent::__construct(0, $e);
        }

    }

}