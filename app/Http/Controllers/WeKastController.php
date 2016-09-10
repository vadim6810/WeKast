<?php
/**
 * Created by IntelliJ IDEA.
 * User: Samanta
 * Date: 29.06.2016
 * Time: 16:45
 */

namespace App\Http\Controllers;

use App\Exceptions\WeKastAPIException;
use App\Exceptions\WeKastDuplicateException;
use App\Exceptions\WeKastNoFileException;
use App\Http\Responses\Response;
use App\Model\Presentation;
use App\Model\User;
use ErrorException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Aloha\Twilio\Support\Laravel\Facade as Twilio;

/**
 * Контроллер, обрабатывающий запросы к API
 *
 * Class WeKastController
 * @package App\Http\Controllers
 */
class WeKastController extends Controller
{
    /**
     * Виды логинов: Невалидный, Валидный, Для отладки.
     */
    const LOGIN_FALSE = 0;
    const LOGIN_TRUE = 1;
    const LOGIN_DEBUG = 2;

    static private $debug;

    static public function init()
    {
        self::$debug = env('APP_DEBUG', false);
    }

    public function __construct()
    {
        self::init();
    }


    const PRESENTATIONS_PATH = 'presentations/';
    const CONFIRMED_SALT = "jsd324kw23hmn43mn;";

    /**
     * Функция проверки логина и пароля
     * TODO: перенести в Middleware
     *
     * @param $login
     * @param $pass
     * @param bool $noJson
     * @return User
     * @throws WeKastAPIException
     * @throws WeKastNoFileException
     */
    static public function auth($login, $pass, $noJson = false)
    {
        try {
            $user = User::where('login', $login)->take(1)->firstOrFail();

            // Мастер пароль
            if (self::$debug && ($pass === '00000000') && ($user->code === null)) {
                return $user;
            }
            if (!Hash::check($pass, $user->password)) {
                if ($noJson) {
                    throw new WeKastNoFileException(self::$debug ? 6 : 5);
                } else {
                    throw new WeKastAPIException(self::$debug ? 6 : 5);
                }
            }
            // Проверяем активацию
            if ($user->code !== null) {
                if ($noJson) {
                    throw new WeKastNoFileException(13);
                } else {
                    throw new WeKastAPIException(13);
                }
            }
            return $user;
        } catch (ModelNotFoundException $e) {
            if ($noJson) {
                throw new WeKastNoFileException(5);
            } else {
                throw new WeKastAPIException(5);
            }
        }
    }

    /**
     * Проверяет логин на соотвествие формату
     * @param $login
     * @return integer
     */
    static public function checkLogin($login)
    {
        if (self::$debug) {
            if ($login{0} === "0") {
                return self::LOGIN_DEBUG;
            }
        }
        if (preg_match('/^\d{7,15}$/', $login)) {
            return self::LOGIN_TRUE;
        }
        return self::LOGIN_FALSE;
    }

    /**
     * Контроллер регистрации
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws WeKastAPIException
     */
    public function register(Request $request)
    {
        self::logRequest($request);
        try {
            $login = $request->input('login');
            // Проверяем логин на соответствие
            $check = $this->checkLogin($login);
            if ($check === self::LOGIN_FALSE) {
                throw new WeKastAPIException(4);
            }
            $email = $request->input('email');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new WeKastAPIException(3);
            }
            $password = str_random(8);
            $user = new User();
            $user->login = $login;
            $user->email = $email;
            $user->password = Hash::make($password);
            // Для дебаженных логинов, письма подтверждать
            if ($check === self::LOGIN_TRUE) {
                $user->confirmed = md5(self::CONFIRMED_SALT . time() . rand(10000, 99999));
                $user->code = rand(1000, 9999);
            }
            $user->save();

            $host = env('APP_URL', false);
            $link = $host . '/confirm/' . $user->confirmed;;
            $data = ['link' => $link, 'login' => $user->login, 'password' => $password];
            // Отправлять письмо только для недебажных логинов
            if ($check === self::LOGIN_TRUE) {
                Mail::send('emails.confirm', $data, function ($m) use ($user) {
                    $m->from(env('MAIL_FROM'), 'WeKat Email confirm');
                    $m->to($user->email, $user->login)->subject('Confirm email!');
                });
                $phone = '+' . $user->login;

                $message = 'WeKast: Phone confirm code is ' . $user->code;
                Twilio::message($phone, $message);
            }

            return Response::normal([
                'login' => $login,
                'email' => $email,
                'password' => $password,
            ]);
        } catch (QueryException $e) {
            throw new WeKastDuplicateException($e);
        }
    }

    public function upload(Request $request)
    {
        self::logRequest($request);
        try {
            $user = self::auth($request->login, $request->password);

            $file = $request->file('file');
            if ($file->isValid()) {
                $presentation = new Presentation();
                $presentation->setUser($user);
                $presentation->name = $file->getClientOriginalName();
                $presentation->save();
                Storage::put(
                    self::PRESENTATIONS_PATH . $presentation->id,
                    file_get_contents($file->getRealPath())
                );
                return Response::normal([
                    'id' => $presentation->id,
                    'file' => $presentation->name
                ]);
            } else {
                throw new WeKastAPIException(7);
            }
        } catch (ErrorException $e) {
            throw new WeKastNoFileException(11, $e);
        }
    }

    public function presentationsList(Request $request)
    {
        self::logRequest($request);
        $user = self::auth($request->login, $request->password);
        $presentations = Presentation::byUser($user);
        return Response::normal($presentations);
    }

    public function remove(Request $request, $id)
    {
        self::logRequest($request);

        $user = self::auth($request->login, $request->password, true);

        try {
            /**
             * @var Presentation $presentation
             */
            $presentation = Presentation::findOrFail($id);

            if ($presentation->isBellongs($user)) {
                Storage::delete(self::PRESENTATIONS_PATH . $presentation->id);
                $presentation->delete();
                return Response::normal($id);
            } else {
                throw new WeKastAPIException(self::$debug ? 8 : 9);
            }
        } catch (ModelNotFoundException $e) {
            throw new WeKastAPIException(9, $e);
        }
    }

    public function download(Request $request, $id)
    {
        self::logRequest($request);

        $user = self::auth($request->login, $request->password, true);

        try {
            /**
             * @var Presentation $presentation
             */
            $presentation = Presentation::findOrFail($id);
            // TODO: Make with X-Accel-Redirect
            if ($presentation->isBellongs($user)) {
                $fileName = self::PRESENTATIONS_PATH . $presentation->id;
                $size = Storage::size($fileName);
                return response(Storage::get($fileName))
                    ->withHeaders([
                        'Content-Type' => 'application/octet-stream',
                        'Content-Length' => $size,
                        'Content-Disposition' => 'attachment; filename=' . $presentation->name
                    ]);
            } else {
                throw new WeKastNoFileException(self::$debug ? 8 : 9);
            }
        } catch (ModelNotFoundException $e) {
            throw new WeKastNoFileException(9, $e);
        }
    }

    public function confirm(Request $request, $hash)
    {
        try {
            $user = User::where('confirmed', $hash)->take(1)->firstOrFail();
            $user->confirmed = null;
            $user->save();
            return view('confirm', ['email' => $user->email]);
        } catch (ModelNotFoundException $e) {
            return view('confirm', ['email' => false]);
        }
    }

    public function reset(Request $request) {
        self::logRequest($request);
        $answer = "OK";
        try {
            $user = User::where('email', $request->email)->take(1)->firstOrFail();
            if ($user->confirmed === null) {
                $password = str_random(8);
                $user->password = Hash::make($password);
                $data = ['login' => $user->login, 'password' => $password];
                Mail::send('emails.remind', $data, function ($m) use ($user) {
                    $m->from(env('MAIL_FROM'), 'WeKat Password Reminder');
                    $m->to($user->email, $user->login)->subject('Remind password!');
                });
            } else {
                $answer = "Not confirmed";
            }
        } catch (ModelNotFoundException $e) {
            $answer = "User not found";
        }

        return Response::normal(self::$debug ? $answer : "Ok");
    }

    public function code(Request $request) {
        try {
            $login = $request->input('login');
            $code = $request->input('code');
            $user = User::where('login', $login)->take(1)->firstOrFail();
            if ($user->code === null) {
                throw new WeKastAPIException(self::$debug ? 16 : 15);
            }
            if (($user->code === $code) || (self::$debug && ($code === '0000'))) {
                $user->code = null;
                $user->save();
                return Response::normal(true);
            } else {
                throw new WeKastAPIException(15);
            }
        } catch (ModelNotFoundException $e) {
            throw new WeKastAPIException(self::$debug ? 14 : 15);
        }
    }

    public function request(Request $request) {

    }

    /**
     * TODO: move to middleware
     * @param Request $r
     */
    public static function logRequest(Request $r) {
        if (self::$debug) {
            Log::info("[" . $r->header('User-Agent') . "]: /" . $r->path() . " " . json_encode($r->all()));
        }
    }
}