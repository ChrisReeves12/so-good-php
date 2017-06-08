<?php


namespace App\Services\Contracts;

/**
 * Interface IAdminSearchable
 * @package App
 */
interface IAdminSearchable
{
  /**
   * Returns results for admin search
   * @param string $keyword
   * @return array
   */
  public function handleAdminSearch(string $keyword): array;
}