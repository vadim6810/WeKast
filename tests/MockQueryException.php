<?php
use Illuminate\Database\QueryException;

/**
 * Created by IntelliJ IDEA.
 * User: Samanta
 * Date: 19.07.2016
 * Time: 16:43
 */
class MockQueryException extends QueryException
{

    public function __construct($message)
    {
        $this->errorInfo = array(
            "23000",
            1062,
            $message
        );
    }

}