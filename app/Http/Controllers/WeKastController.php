<?php
/**
 * Created by IntelliJ IDEA.
 * User: Samanta
 * Date: 29.06.2016
 * Time: 16:45
 */

namespace App\Http\Controllers;


use App\Model\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class WeKastController extends Controller
{

    public function register(Request $request)
    {
        try {
            $login = $request->input('login');
            if (mb_strlen($login) < 6) {
                return response()->json([
                    'err' => 'Min 6 symbols in login',
                    'num' => 4,
                ]);
            }
            $email = $request->input('email');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'err' => 'Error email format',
                    'num' => 3,
                ]);
            }
            $password = str_random(8);
            $user = new User();
            $user->login = $login;
            $user->email = $email;
            $user->password = bcrypt($password);
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
                        return response()->json([
                            'err' => 'Duplicate ' . $m[1],
                            'num' => isset($errors[$m[1]]) ? $errors[$m[1]] : 0
                        ]);
                    }
                    break;
                default:
                    return response()->json([
                        'err' => 'Error',
                        'num' => 0,
                    ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'err' => 'Error',
                'num' => 0,
            ]);
        }
    }
}