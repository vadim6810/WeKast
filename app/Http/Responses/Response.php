<?php
/**
 * Created by IntelliJ IDEA.
 * User: Samanta
 * Date: 19.07.2016
 * Time: 19:45
 */

namespace App\Http\Responses;

/**
 * Генератор ответа
 *
 * Class Response
 * @package App\Http\Responses
 */
class Response
{
    /**
     * Нормальный ответ
     * @param $answer
     * @return \Illuminate\Http\JsonResponse
     */
    static public function normal($answer)
    {
        return self::response([
            'status' => 0,
            'answer' => $answer,
        ]);
    }

    /**
     * Ответ с ошибкой
     * @param $code
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    static public function error($code, $message)
    {
        return self::response([
            'status' => $code,
            'error' => $message,
        ]);
    }

    static private function response($data)
    {
        return response()->json($data);
    }

}