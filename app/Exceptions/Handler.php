<?php

namespace App\Exceptions;

use App\Services\AdminLoggerService;
use App\Services\FrontendLoggerService;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{

  /**
   * A list of the exception types that should not be reported.
   *
   * @var array
   */
  protected $dontReport = [
    \Illuminate\Auth\AuthenticationException::class,
    \Illuminate\Auth\Access\AuthorizationException::class,
    \Symfony\Component\HttpKernel\Exception\HttpException::class,
    \Illuminate\Database\Eloquent\ModelNotFoundException::class,
    \Illuminate\Session\TokenMismatchException::class,
    \Illuminate\Validation\ValidationException::class,
  ];

  /** @var AdminLoggerService */
  protected $adminLoggerService;

  /** @var FrontendLoggerService */
  protected $frontendLoggerService;


  public function __construct(Container $container, FrontendLoggerService $frontendLoggerService, AdminLoggerService $adminLoggerService)
  {
    parent::__construct($container);
    $this->adminLoggerService = $adminLoggerService;
    $this->frontendLoggerService = $frontendLoggerService;
  }

  /**
   * Report or log an exception.
   *
   * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
   *
   * @param  \Exception $exception
   * @return void
   */
  public function report(Exception $exception)
  {
    if(!in_array(get_class($exception), $this->dontReport) && !($exception instanceof NotFoundHttpException))
    {
      if(preg_match('/Http\\\Controllers\\\Frontend/i', $exception->getTraceAsString()))
      {
        $this->frontendLoggerService->logException('error', $exception);
      }
      elseif(preg_match('/Http\\\Controllers\\\Admin/i', $exception->getTraceAsString()))
      {
        $this->adminLoggerService->logException('error', $exception);
      }
    }

    parent::report($exception);
  }

  /**
   * Render an exception into an HTTP response.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  \Exception $exception
   * @return \Illuminate\Http\Response
   */
  public function render($request, Exception $exception)
  {
    return parent::render($request, $exception);
  }

  /**
   * Convert an authentication exception into an unauthenticated response.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  \Illuminate\Auth\AuthenticationException $exception
   * @return \Illuminate\Http\Response
   */
  protected function unauthenticated($request, AuthenticationException $exception)
  {
    if($request->expectsJson())
    {
      return response()->json(['error' => 'Unauthenticated.'], 401);
    }

    return redirect()->guest('login');
  }
}
