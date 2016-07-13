<?php
/**
 * Created by IntelliJ IDEA.
 * User: Samanta
 * Date: 29.06.2016
 * Time: 16:45
 */

namespace App\Http\Controllers;


use App\Exceptions\WeKastAPIException;
use App\Model\Presentation;
use App\Model\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Контроллер, обрабатывающий запросы к API
 *
 * Class WeKastController
 * @package App\Http\Controllers
 */
class WeKastController extends Controller
{
    /**
     * Функция проверки логина и пароля
     *
     * @param $login
     * @param $pass
     * @return User
     * @throws WeKastAPIException
     */
    static public function auth($login, $pass)
    {
        try {
            $user = User::where('login', $login)->take(1)->firstOrFail();
            if (!Hash::check($pass, $user->password)) {
                $debug = env('APP_DEBUG', false);
                throw new WeKastAPIException($debug ? 6 : 5);
            }
            return $user;
        } catch (ModelNotFoundException $e) {
            throw new WeKastAPIException(5);
        }
    }

    /**
     * Контроллер регистрации
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws WeKastAPIException
     */
    public function register(Request $request)
    {
        try {
            $login = $request->input('login');
            // Проверяем логин на соответствие TODO вынести проверки в Middleware
            if (mb_strlen($login) < 6) {
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

            $user->save();

            return response()->json([
                'login' => $login,
                'email' => $email,
                'password' => $password
            ]);
        } catch (QueryException $e) {
            switch ($e->errorInfo[0]) {
                case '23000':
                    $m = array();
                    if (preg_match('#^UNIQUE constraint failed: users\.(\w+)$#', $e->errorInfo[2], $m)) {
                        $errors = [
                            'login' => 1,
                            'email' => 2,
                        ];
                        throw new WeKastAPIException(isset($errors[$m[1]]) ? $errors[$m[1]] : 0);
                    }
                    break;
                default:
                    throw new WeKastAPIException(0);
            }
            return null;
        }
    }

    public function upload(Request $request)
    {
        $user = self::auth($request->login, $request->password);

        $file = $request->file('file');
        if ($file->isValid()) {
            $presentation = new Presentation();
            $presentation->setUser($user);
            $presentation->name = $file->getClientOriginalName();
            $presentation->save();
            Storage::put(
                'presentations/' . $presentation->id,
                file_get_contents($file->getRealPath())
            );
            return response()->json([
                'id' => $presentation->id,
                'file' => $presentation->name
            ]);
        } else {
            throw new WeKastAPIException(7);
        }
    }

    public function presentationsList(Request $request)
    {
        $user = self::auth($request->login, $request->password);
        $presentations = Presentation::byUser($user);
        return $presentations->toJson();
    }
}