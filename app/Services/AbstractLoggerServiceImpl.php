<?php
/**
 * The LoggerServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\ILoggerService;
use App\Log;
use Illuminate\Support\Collection;

/**
 * Class AbstractLoggerServiceImpl
 * @package App\Services
 */
abstract class AbstractLoggerServiceImpl implements ILoggerService
{
  /**
   * Write a debug message
   * @param string $message
   * @param array $extra_data
   */
  abstract public function debug(string $message, array $extra_data = []);

  /**
   * Write an info message
   * @param string $message
   * @param array $extra_data
   */
  abstract public function info(string $message, array $extra_data = []);

  /**
   * Write a critical message
   * @param string $message
   * @param array $extra_data
   */
  abstract public function critical(string $message, array $extra_data = []);

  /**
   * Write an emergency message
   * @param string $message
   * @param array $extra_data
   */
  abstract public function emergency(string $message, array $extra_data = []);

  /**
   * Write an error message
   * @param string $message
   * @param array $extra_data
   */
  abstract public function error(string $message, array $extra_data = []);

  /**
   * Logs out an exception
   * @param string $log_level
   * @param \Exception $exception
   */
  abstract public function logException(string $log_level, \Exception $exception);

  /**
   * Find logs by given type
   * @param array $types
   * @param string $location
   * @return Collection
   */
  public function findByTypeArray(array $types, string $location)
  {
    return Log::whereIn('type', ['EMERGENCY', 'ERROR', 'CRITICAL'])->orderBy('created_at', 'DESC')->take(40)->get();
  }
}