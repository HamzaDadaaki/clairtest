<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (PostTooLargeException $exception, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The selected files are too large for one request. Use smaller images or upload fewer files at once.',
                ], 413);
            }

            return back()->with(
                'error',
                'The selected files are too large for one request. This version now uploads product images one by one automatically, but if you still see this message, reduce the image sizes or raise post_max_size and upload_max_filesize in PHP.'
            );
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
