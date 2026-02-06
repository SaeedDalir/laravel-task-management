<?php

namespace App\Providers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro(
            'general',
            function ($message, $data, $code, $success, $meta = []): JsonResponse {
                $code = $code < 1000 && $code >= 200 ? $code : 400;

                $result = [
                    'success' => $success,
                    'code' => $code,
                    'message' => $message,
                    'data' => $data,
                    'meta' => $meta,
                ];

                return response()->json($result, $code);
            }
        );

        Response::macro(
            'success',
            function (
                $message = '',
                $data = [],
                $code = HttpFoundationResponse::HTTP_OK,
                $meta = [],
            ): JsonResponse {
                return response()->general($message, $data, $code, true, $meta);
            }
        );

        Response::macro(
            'error',
            function (
                $message = '',
                $data = [],
                $code = HttpFoundationResponse::HTTP_BAD_REQUEST
            ): JsonResponse {
                return response()->general($message, $data, $code, false);
            }
        );
    }
}
