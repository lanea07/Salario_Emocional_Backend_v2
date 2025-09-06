<?php

use App\Framework\Facades\ApiResponse;
use App\Framework\Enums\HttpStatusCodes;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ApiResponse::sendResponse(message: __('messages.web_access__api_mode_only'), httpCode: HttpStatusCodes::FORBIDDEN_403);
});
