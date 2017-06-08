<?php
/**
 * The IPopupService interface definition.
 *
 * All popup services should implement this
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services\Contracts;
use App\Popup;

/**
 * Interface IPopupService
 * @package App\Services\Contracts
 */
interface IPopupService
{
  /**
   * Locate popup by internal name
   * @param string $internal_name
   */
  public function findPopupByInternalName(string $internal_name);

  /**
   * Get if popup should show or not
   * @param Popup $popup
   * @param array $page_data
   * @return bool
   */
  public function shouldPopupShow(Popup $popup, array $page_data): bool;
}