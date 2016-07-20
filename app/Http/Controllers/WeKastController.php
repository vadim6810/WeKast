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
use Illuminate\Support\Facades\Storage;

/**
 * Контроллер, обрабатывающий запросы к API
 *
 * Class WeKastController
 * @package App\Http\Controllers
 */
class WeKastController extends Controller
{
    const PRESENTATIONS_PATH = 'presentations/';

    /**
     * Функция проверки логина и пароля
     *
     * @param $login
     * @param $pass
     * @return User
     * @throws WeKastAPIException
     */
    static public function auth($login, $pass, $noJson = false)
    {
        try {
            $user = User::where('login', $login)->take(1)->firstOrFail();
            if (!Hash::check($pass, $user->password)) {
                $debug = env('APP_DEBUG', false);
                if ($noJson) {
                    throw new WeKastNoFileException($debug ? 6 : 5);
                } else {
                    throw new WeKastAPIException($debug ? 6 : 5);
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
     * Контроллер регистрации
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws WeKastAPIException
     */
    public function register(Request $request)
    {
        try {
            $login = $request->input('login');
            // Проверяем логин на соответствие TODO вынести проверки в Middleware?
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

            return Response::normal([
                'login' => $login,
                'email' => $email,
                'password' => $password
            ]);
        } catch (QueryException $e) {
            throw new WeKastDuplicateException($e);
        }
    }

    public function upload(Request $request)
    {
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
        $user = self::auth($request->login, $request->password);
        $presentations = Presentation::byUser($user);
        return Response::normal($presentations);
    }

    public function download(Request $request, $id) {
        $user = self::auth($request->login, $request->password, true);

        try {
            $presentation = Presentation::findOrFail($id);
            // TODO: Make with X-Accel-Redirect
            if ($presentation->isBellongs($user)) {
                return response(Storage::get(self::PRESENTATIONS_PATH . $presentation->id))
                    ->withHeaders([
                        'Content-Type' => 'application/octet-stream',
                        'Content-Disposition' => 'attachment; filename=' . $presentation->name
                    ]);
            } else {
                $debug = env('APP_DEBUG', false);
                throw new WeKastNoFileException($debug ? 8 : 9);
            }
        } catch (ModelNotFoundException $e) {
            throw new WeKastNoFileException(9, $e);
        }
    }
}