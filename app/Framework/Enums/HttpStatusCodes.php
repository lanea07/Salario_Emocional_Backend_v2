<?php

namespace App\Framework\Enums;

/**
 * Contains all common http status codes. Provides standard enumeration for use in API responses.
 */
enum HttpStatusCodes: int
{

    // 1xx: Informational
    case CONTINUE_100 = 100;
    case SWITCHING_PROTOCOLS_101 = 101;
    case PROCESSING_102 = 102;
    case EARLY_HINTS_103 = 103;

        // 2xx: Success
    case OK_200 = 200;
    case CREATED_201 = 201;
    case ACCEPTED_202 = 202;
    case NON_AUTHORITATIVE_INFORMATION_203 = 203;
    case NO_CONTENT_204 = 204;
    case RESET_CONTENT_205 = 205;
    case PARTIAL_CONTENT_206 = 206;
    case MULTI_STATUS_207 = 207;
    case ALREADY_REPORTED_208 = 208;
    case IM_USED_226 = 226;

        // 3xx: Redirection
    case MULTIPLE_CHOICES_300 = 300;
    case MOVED_PERMANENTLY_301 = 301;
    case FOUND_302 = 302;
    case SEE_OTHER_303 = 303;
    case NOT_MODIFIED_304 = 304;
    case TEMPORARY_REDIRECT_307 = 307;
    case PERMANENT_REDIRECT_308 = 308;

        // 4xx: Client Error
    case BAD_REQUEST_400 = 400;
    case UNAUTHORIZED_401 = 401;
    case PAYMENT_REQUIRED_402 = 402;
    case FORBIDDEN_403 = 403;
    case NOT_FOUND_404 = 404;
    case METHOD_NOT_ALLOWED_405 = 405;
    case NOT_ACCEPTABLE_406 = 406;
    case PROXY_AUTHENTICATION_REQUIRED_407 = 407;
    case REQUEST_TIMEOUT_408 = 408;
    case CONFLICT_409 = 409;
    case GONE_410 = 410;
    case LENGTH_REQUIRED_411 = 411;
    case PRECONDITION_FAILED_412 = 412;
    case PAYLOAD_TOO_LARGE_413 = 413;
    case URI_TOO_LONG_414 = 414;
    case UNSUPPORTED_MEDIA_TYPE_415 = 415;
    case RANGE_NOT_SATISFIABLE_416 = 416;
    case EXPECTATION_FAILED_417 = 417;
    case IM_A_TEAPOT_418 = 418;
    case MISDIRECTED_REQUEST_421 = 421;
    case UNPROCESSABLE_ENTITY_422 = 422;
    case LOCKED_423 = 423;
    case FAILED_DEPENDENCY_424 = 424;
    case TOO_EARLY_425 = 425;
    case UPGRADE_REQUIRED_426 = 426;
    case PRECONDITION_REQUIRED_428 = 428;
    case TOO_MANY_REQUESTS_429 = 429;
    case REQUEST_HEADER_FIELDS_TOO_LARGE_431 = 431;
    case UNAVAILABLE_FOR_LEGAL_REASONS_451 = 451;

        // 5xx: Server Error
    case INTERNAL_SERVER_ERROR_500 = 500;
    case NOT_IMPLEMENTED_501 = 501;
    case BAD_GATEWAY_502 = 502;
    case SERVICE_UNAVAILABLE_503 = 503;
    case GATEWAY_TIMEOUT_504 = 504;
    case HTTP_VERSION_NOT_SUPPORTED_505 = 505;
    case VARIANT_ALSO_NEGOTIATES_506 = 506;
    case INSUFFICIENT_STORAGE_507 = 507;
    case LOOP_DETECTED_508 = 508;
    case NOT_EXTENDED_510 = 510;
    case NETWORK_AUTHENTICATION_REQUIRED_511 = 511;
}
