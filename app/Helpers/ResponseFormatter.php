<?php

namespace App\Helpers;

/**
 * Format response.
 */
class ResponseFormatter
{
    /**
     * API Response
     *
     * @var array
     */
    protected static $response = [
        'success' => true,
    ];

    /**
     * Give success response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function success($data = null, $message = null, $code = 200)
    {
        self::$response['message'] = $message;
        self::$response['result'] = $data;

        return response()->json(self::$response, $code);
    }

    /**
     * Give error response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public static function error($message = null, $code = 400)
    {
        self::$response['success'] = false;
        self::$response['error']['code'] = $code;
        self::$response['error']['message'] = $message;

        return response()->json(self::$response, $code);
    }
}
