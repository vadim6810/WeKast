<?php
/**
 * Created by IntelliJ IDEA.
 * User: Samanta
 * Date: 29.06.2016
 * Time: 16:45
 */

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class WeKastController extends Controller
{

    public function register(Request $request)
    {
        return response()->json([
            'login' => $request->input('login'),
            'email' => $request->input('email'),
            'password' => 'stub_password'
        ]);
    }
}