<?php

namespace App\Framework\Factories;

use App\Framework\Contracts\ApiResponseContract;
use App\Framework\Services\ApiV1ResponseService;
use App\Framework\Services\ApiV2ResponseService;

class ApiResponseFactory
{

    public static function make(): ApiResponseContract
    {
        try {
            $version = app('app.api.version');
        } catch (\Exception $e) {
            // Fallback to default version if not set
            $version = 'v1';
        }

        if (str_starts_with($version, 'v2')) {
            return new ApiV2ResponseService();
        }

        // Default to v1 if no specific version is detected
        return new ApiV1ResponseService();
    }
}
