<?php

namespace App\Factories;

use App\Contracts\ApiResponseContract;
use App\Services\ApiV1ResponseService;
use App\Services\ApiV2ResponseService;

class ApiResponseFactory {

    public static function make(): ApiResponseContract {

        $path = request()->query('version', 'v1');

        if (str_starts_with($path, 'v2')) {
            return new ApiV2ResponseService();
        }

        // Default to v1 if no specific version is detected
        return new ApiV1ResponseService();
    }
}
