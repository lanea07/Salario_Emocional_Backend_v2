<?php

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ApiResponse::sendResponse(message: __('messages.web_access__api_mode_only'), httpCode: HttpStatusCodes::FORBIDDEN_403);
});