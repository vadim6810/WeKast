<?php

use App\Exceptions\WeKastAPIException;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        $this->visit('/')
             ->see('API tester');
    }

    public function testException()
    {



        $e = new WeKastAPIException(3);
        $this->assertTrue($e->getMessage() === WeKastAPIException::$errors[3]);

        $err = "My test err";
        $prev = new \Exception($err);
        $e = new WeKastAPIException(0, $prev);

        $debug = env('APP_DEBUG', false);
        $message = WeKastAPIException::$errors[0] . ($debug ? ': ' . $prev->getMessage() : '');
        $this->assertTrue($e->getMessage() === $message);
    }
}
