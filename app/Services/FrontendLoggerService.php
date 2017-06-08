<?php
/**
 * The FrontendLoggerService class definition.
 *
 * Default implementation of the ILoggerService for the Frontend
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class FrontendLoggerService
 * @package App\Services
 */
class FrontendLoggerService extends AbstractLoggerServiceImpl
{
  protected $logger;

  /**
   * FrontendLoggerService constructor.
   */
  public function __construct()
  {
    $this->logger = new Logger('Frontend');

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/frontend/debug.log', Logger::DEBUG, false))
      ->setFormatter(new LineFormatter(null, null, true)));

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/frontend/info.log', Logger::INFO, false))
      ->setFormatter(new LineFormatter(null, null, true)));

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/frontend/warning.log', Logger::WARNING, false))
      ->setFormatter(new LineFormatter(null, null, true)));

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/frontend/error.log', Logger::ERROR, false))
      ->setFormatter(new LineFormatter(null, null, true)));

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/frontend/error.log', Logger::CRITICAL, false))
      ->setFormatter(new LineFormatter(null, null, true)));

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/frontend/error.log', Logger::EMERGENCY, false))
      ->setFormatter(new LineFormatter(null, null, true)));
  }

  /**
   * Write a debug message
   * @param string $message
   * @param array $extra_data
   * @param bool $save_to_database
   */
  public function debug(string $message, array $extra_data = [], bool $save_to_database = true)
  {
    $this->logger->debug($message, $extra_data);
  }

  /**
   * Write info message
   * @param string $message
   * @param array $extra_data
   * @param bool $save_to_database
   */
  public function info(string $message, array $extra_data = [], bool $save_to_database = true)
  {
    $this->logger->info($message, $extra_data);
  }

  /**
   * Write an error message
   * @param string $message
   * @param array $extra_data
   * @param bool $save_to_database
   */
  public function error(string $message, array $extra_data = [], bool $save_to_database = true)
  {
    $this->logger->error($message, $extra_data);

    if($save_to_database)
    {
      Log::create([
        'type'        => 'ERROR',
        'message'     => $message,
        'code'        => $extra_data['code'] ?? null,
        'line'        => $extra_data['line'] ?? null,
        'stack_trace' => $extra_data['stack_trace'] ?? null,
        'extra_data'  => json_encode($extra_data)
      ]);
    }
  }

  /**
   * Add critical message
   * @param string $message
   * @param array $extra_data
   * @param bool $save_to_database
   */
  public function critical(string $message, array $extra_data = [], bool $save_to_database = true)
  {
    $this->logger->critical($message, $extra_data);

    if($save_to_database)
    {
      Log::create([
        'type'        => 'CRITICAL',
        'message'     => $message,
        'code'        => $extra_data['code'] ?? null,
        'line'        => $extra_data['line'] ?? null,
        'stack_trace' => $extra_data['stack_trace'] ?? null,
        'extra_data'  => json_encode($extra_data)
      ]);
    }
  }

  /**
   * Add emergency message
   * @param string $message
   * @param array $extra_data
   * @param bool $save_to_database
   */
  public function emergency(string $message, array $extra_data = [], bool $save_to_database = true)
  {
    $this->logger->emergency($message, $extra_data);

    if($save_to_database)
    {
      Log::create([
        'type'        => 'EMERGENCY',
        'message'     => $message,
        'code'        => $extra_data['code'] ?? null,
        'line'        => $extra_data['line'] ?? null,
        'stack_trace' => $extra_data['stack_trace'] ?? null,
        'extra_data'  => json_encode($extra_data)
      ]);
    }
  }

  /**
   * Write a warning message
   * @param string $message
   * @param array $extra_data
   * @param bool $save_to_database
   */
  public function warning(string $message, array $extra_data = [], bool $save_to_database = true)
  {
    $this->logger->warning($message, $extra_data);

    if($save_to_database)
    {
      Log::create([
        'type'        => 'WARNING',
        'message'     => $message,
        'code'        => $extra_data['code'] ?? null,
        'line'        => $extra_data['line'] ?? null,
        'stack_trace' => $extra_data['stack_trace'] ?? null,
        'extra_data'  => json_encode($extra_data)
      ]);
    }
  }

  /**
   * Logs out an exception
   * @param string $log_level
   * @param \Exception $exception
   */
  public function logException(string $log_level, \Exception $exception)
  {
    $log_level = strtolower(trim($log_level));

    $message = "\n" . $exception->getFile() . "\n";
    $message .= "LINE: " . $exception->getLine() . "\n";
    $message .= "CODE: " . $exception->getCode() . " - " . $exception->getMessage() . "\n";
    $message .= "================================================\n";
    $message .= $exception->getTraceAsString() . "\n\n";

    // Save to database
    Log::create([
      'message' => $exception->getMessage(),
      'line' => $exception->getLine(),
      'code' => $exception->getCode(),
      'file' => $exception->getFile(),
      'stack_trace' => $exception->getTraceAsString(),
      'type' => 'ERROR'
    ]);

    if(method_exists($this->logger, $log_level))
      $this->{$log_level}($message, [
        'code' => $exception->getCode(),
        'line' => $exception->getLine(),
        'file' => $exception->getFile(),
        'stack_trace' => $exception->getTraceAsString()
      ], false);
  }
}