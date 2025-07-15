<?php

namespace App\Framework\Middleware;

use App\Framework\Enums\HttpStatusCodes;
use App\Framework\Facades\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiVersion
{

    // Define the allowed versions
    protected array $allowedVersions = ['v1', 'v2'];

    public function handle(Request $request, Closure $next): Response
    {
        $version = $request->route('version');
        $request->route()->forgetParameter('version');

        if (!in_array($version, $this->allowedVersions, true)) {
            $data = [
                'message' => __('middlewares/validate-api-version.unsupported_api_version'),
                'supported_versions' => $this->allowedVersions,
            ];
            return ApiResponse::sendResponse($data, __('middlewares/validate-api-version.unsupported_api_version'), HttpStatusCodes::BAD_REQUEST_400, false);
        }

        return $next($request);
    }
}
