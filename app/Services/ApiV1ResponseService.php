<?php

namespace App\Services;

use App\Contracts\ApiResponseContract;
use App\Enums\HttpStatusCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Cookie;

class ApiV1ResponseService implements ApiResponseContract {

    public function sendResponse(mixed $data = [], string $message = '', HttpStatusCodes $httpCode = HttpStatusCodes::OK_200, bool $resetJWT = false, ?Cookie $cookie = null): JsonResponse {
        if ($resetJWT) {
            $token = Auth::refresh();
            $cookie = cookie('token', $token, env('COOKIE_LIFETIME', 60), null, null, true, true, false, 'Strict');
        }

        $responsePayload = [
            'data' => $data,
            'message' => $message
        ];

        $response = response()->json($responsePayload, $httpCode->value);

        if ($cookie) {
            $response->withCookie($cookie);
        }

        return $response;
    }
}
