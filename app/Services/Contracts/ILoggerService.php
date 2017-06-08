<?php


namespace App\Services\Contracts;

use Illuminate\Support\Collection;

/**
 * Interface LoggerService
 * @package App\Services
 */
interface ILoggerService
{
  /**
   * Write a debug message
   * @param string $message
   * @param array $extra_data
   */
  public function debug(string $message, array $extra_data = []);

  /**
   * Write an info message
   * @param string $message
   * @param array $extra_data
   */
  public function info(string $message, array $extra_data = []);

  /**
   * Write a critical message
   * @param string $message
   * @param array $extra_data
   */
  public function critical(string $message, array $extra_data = []);

  /**
   * Write an emergency message
   * @param string $message
   * @param array $extra_data
   */
  public function emergency(string $message, array $extra_data = []);

  /**
   * Write an error message
   * @param string $message
   * @param array $extra_data
   */
  public function error(string $message, array $extra_data = []);

  /**
   * Logs out an exception
   * @param string $log_level
   * @param \Exception $exception
   */
  public function logException(string $log_level, \Exception $exception);

  /**
   * Find logs by given type
   * @param array $types
   * @param string $location
   * @return Collection
   */
  public function findByTypeArray(array $types, string $location);
}