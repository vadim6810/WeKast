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
        $message = WeKastAPIException::$errors[10] . ($debug ? ': ' . $prev->getMessage() : '');
        $this->assertTrue($e->getMessage() === $message);
    }

    public function testDuplicateException()
    {
        require_once 'MockQueryException.php';
        $msgs = [];
        $msgs[] = ["Duplicate entry 'aaaaa@bbb.cc' for key 'users_login_unique'", 1];
        $msgs[] = ["Duplicate entry 'aaaaa@bbb.cc' for key 'users_email_unique'", 2];
        $msgs[] = ["UNIQUE constraint failed: users.login", 1];
        $msgs[] = ["UNIQUE constraint failed: users.email", 2];
        $msgs[] = [",kf,k sajoewijrwe bla", 10];

        foreach ($msgs as $msg) {
            $e = new MockQueryException($msg[0]);
            $exception = new \App\Exceptions\WeKastDuplicateException($e);
            $this->assertTrue($exception->getCode() === $msg[1]);
        }
    }

    public function testLoginCheck()
    {
        $debug = env('APP_DEBUG', false);
        \App\Http\Controllers\WeKastController::init();

        $LOGIN_FALSE = \App\Http\Controllers\WeKastController::LOGIN_FALSE;
        $LOGIN_TRUE = \App\Http\Controllers\WeKastController::LOGIN_TRUE;
        $LOGIN_DEBUG = $debug ? \App\Http\Controllers\WeKastController::LOGIN_DEBUG : \App\Http\Controllers\WeKastController::LOGIN_FALSE;

        $logins = [
            'adas' => $LOGIN_FALSE,
            '23424' => $LOGIN_FALSE,
            '2222252324234234234' => $LOGIN_FALSE,
            '2343443234' => $LOGIN_TRUE,
            '' => $LOGIN_FALSE,
            '024323' => $LOGIN_DEBUG,
            '0243232sdfsdf' => $LOGIN_DEBUG,
            '02432323423432423424' => $LOGIN_DEBUG,
            '0' => $LOGIN_DEBUG,
        ];

        foreach ($logins as $login => $result) {
            $this->assertEquals(\App\Http\Controllers\WeKastController::checkLogin($login), $result);
        }
    }
}
