<?php

namespace App\Exceptions;

use App\Facade\CustomFacade;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use League\OAuth2\Server\Exception\OAuthServerException;

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
   * A list of the inputs that are never flashed to the session on validation exceptions.
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
   *
   * @return void
   */
  public function register()
  {
    $this->reportable(function (Throwable $e) {
      //
    });
  }

  protected function unauthenticated($request, AuthenticationException $exception)
  {
    if ($request->expectsJson()) {
      return CustomFacade::errorResponse('Unauthenticated user!');
    }
  }

  public function render($request, Throwable $exception)
  {
    if (!$request->expectsJson()) {
      if ($exception instanceof AuthenticationException) {
        return redirect()->route('login');
      }
    }

    if ($request->expectsJson()) {
      if (
        $exception instanceof
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
      ) {
        return CustomFacade::errorResponse('End point not found!');
      } elseif (
        $exception instanceof
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
      ) {
        return CustomFacade::errorResponse('Method not allowed!');
      } elseif (
        $exception instanceof
        \League\OAuth2\Server\Exception\OAuthServerException &&
        $exception->getCode() == 9
      ) {
        return CustomFacade::errorResponse('Unauthenticated user!');
      } elseif (
        $exception instanceof
        \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException
      ) {
        return CustomFacade::errorResponse('Too many attempt!');
      }

      if (
        $exception instanceof AuthenticationException ||
        $exception instanceof OAuthServerException
      ) {
        return CustomFacade::errorResponse('Unauthenticated');
      }

      if ($exception instanceof QueryException) {
        return CustomFacade::errorResponse(
          'QUERY ISSUE:-' . $exception->getMessage()
        );
      }

      return CustomFacade::errorResponse(
        $exception->getMessage()
      );
    }
    return parent::render($request, $exception);
  }
}
