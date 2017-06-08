<?php
/**
 * The AdminLoggerService class definition.
 *
 * Default implementation of the ILoggerService for the Admin panel
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class AdminLoggerService
 * @package App\Services
 */
class AdminLoggerService extends AbstractLoggerServiceImpl
{
  protected $logger;

  /**
   * AdminLoggerService constructor.
   */
  public function __construct()
  {
    $this->logger = new Logger('Admin');

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/admin/debug.log', Logger::DEBUG, false))
      ->setFormatter(new LineFormatter(null, null, true)));

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/admin/info.log', Logger::INFO, false))
      ->setFormatter(new LineFormatter(null, null, true)));

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/admin/warning.log', Logger::WARNING, false))
      ->setFormatter(new LineFormatter(null, null, true)));

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/admin/error.log', Logger::ERROR, false))
      ->setFormatter(new LineFormatter(null, null, true)));

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/admin/error.log', Logger::CRITICAL, false))
      ->setFormatter(new LineFormatter(null, null, true)));

    $this->logger->pushHandler((new StreamHandler(business('logger_directory') . '/admin/error.log', Logger::EMERGENCY, false))
      ->setFormatter(new LineFormatter(null, null, true)));
  }

  /**
   * Write a debug message
   * @param string $message
   * @param array $extra_data
   */
  public function debug(string $message, array $extra_data = [])
  {
    $this->logger->debug($message, $extra_data);
  }

  public function info(string $message, array $extra_data = [])
  {
    $this->logger->info($message, $extra_data);
  }

  /**
   * Write an error message
   * @param string $message
   * @param array $extra_data
   */
  public function error(string $message, array $extra_data = [])
  {
    $this->logger->error($message, $extra_data);
  }

  /**
   * Add critical message
   * @param string $message
   * @param array $extra_data
   */
  public function critical(string $message, array $extra_data = [])
  {
    $this->logger->critical($message, $extra_data);
  }

  /**
   * Add emergency message
   * @param string $message
   * @param array $extra_data
   */
  public function emergency(string $message, array $extra_data = [])
  {
    $this->logger->emergency($message, $extra_data);
  }

  /**
   * Write a warning message
   * @param string $message
   * @param array $extra_data
   */
  public function warning(string $message, array $extra_data = [])
  {
    $this->logger->warning($message, $extra_data);
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

    if(method_exists($this->logger, $log_level))
      $this->logger->{$log_level}($message);
  }
}