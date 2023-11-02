<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

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
        $this->reportable(function (Throwable $e) {
            //
        });

    }

    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson()) {
            if ($exception instanceof ValidationException) {
                return $this->validationErrorTransformer($exception);
            }

            if ($exception instanceof NotEnabledTransitionException) {
                $transitionBlocker = $exception->getTransitionBlockerList()->getIterator()->current();
                if ($transitionBlocker->getCode() === TransitionBlocker::UNKNOWN) {
                    return response()->json([
                        'message' => $transitionBlocker->getMessage(),
                    ], Response::HTTP_BAD_REQUEST);
                }

                return response()->json([
                    'message' => $exception->getMessage(),
                ], Response::HTTP_BAD_REQUEST);
            }
            if ($exception instanceof RegisterException) {
                return response()->json([
                    'message' => $exception->getMessage(),
                    'registered' => false,
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        }

        return parent::render($request, $exception);
    }

    private function validationErrorTransformer(ValidationException $exception): Response
    {
        $exceptionErrors = $exception->validator->errors()->toArray();
        $errors = [];
        collect($exceptionErrors)
            ->map(function ($item, $key) use (&$errors) {
                data_set($errors, $key, $item[0]);
            });

        return response()->json([
            'message' => $exception->getMessage(),
            'errors' => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
