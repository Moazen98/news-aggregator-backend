<?php

namespace App\Http\Responses\V1;

use Illuminate\Http\JsonResponse;

class CustomResponse extends JsonResponse
{

    public function __construct()
    {
    }

    public static function Success($statusCode = null, $message = '', $data = [], $meta = []): JsonResponse
    {

        $request = Request()->all();
        $meta = $meta + ['mobile-app-info' => isset($request['mobile_app_info']) ? $request['mobile_app_info'] : NULL];
        return response()->json(['status' => true, 'code' => $statusCode ?? 200, 'message' => $message, 'data' => $data, 'meta' => $meta], $statusCode ?? 200);
    }

    public static function Failure($statusCode = null, $message = '', $errors = [], $meta = []): JsonResponse
    {
        $request = Request()->all();
        $meta = $meta + ['mobile-app-info' => isset($request['mobile_app_info']) ? $request['mobile_app_info'] : NULL];
        return response()->json(['status' => false, 'code' => $statusCode ?? 405, 'message' => $message, 'data' => [], 'errors' => $errors, 'meta' => $meta], $statusCode ?? 405);
    }

    public static function Error($statusCode = null, $message = '', $errors = [], $meta = []): JsonResponse
    {
        $request = Request()->all();
        $meta = $meta + ['mobile-app-info' => isset($request['mobile_app_info']) ? $request['mobile_app_info'] : NULL];
        return response()->json(['status' => false, 'code' => $statusCode ?? 403, 'message' => $message, 'data' => [], 'errors' => $errors, 'meta' => $meta], $statusCode ?? 403);
    }
}
