<?php
/**
 * The PopupServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\IPopupService;
use App\Services\Contracts\IRecordCopyable;
use App\Item;
use App\Popup;
use App\RecordType;

/**
 * Class PopupServiceImpl
 * @package App\Services
 */
class PopupServiceImpl implements IPopupService, IRecordCopyable
{

  /**
   * Locate popup by internal name
   * @param string $internal_name
   * @return mixed
   */
  public function findPopupByInternalName(string $internal_name)
  {
    return Popup::where('internal_name', '=', $internal_name)->where('is_inactive', false)->first();
  }

  /**
   * Get if popup should show or not
   * @param Popup $popup
   * @param array $page_data
   * @return bool
   */
  public function shouldPopupShow(Popup $popup, array $page_data): bool
  {
    $ret_val = false;

    if(!isset($_COOKIE[$popup->cookie_name]))
    {
      $ret_val = true;

      // Check for excluded urls and pages
      if(!empty($excluded_uris = $popup->exclude_urls) || !empty($excluded_pages = $popup->exclude_pages))
      {
        if(!empty($page_data['path']) && !empty($excluded_uris) && count(array_filter($excluded_uris, function($url) use($page_data) { return($page_data['path'] == $url); })) > 0)
        {
          $ret_val = false;
        }
        elseif(!empty($page_data['page_title']) && !empty($excluded_pages) && count(array_filter($excluded_pages, function($page_title) use($page_data) { return($page_data['page_title'] == $page_title); })) > 0)
        {
          $ret_val = false;
        }
      }
    }

    return $ret_val;
  }

  /**
   * Copies a single record
   * @param $record
   * @param RecordType $recordType
   * @param bool $record_has_inactive
   * @return array
   */
  public function copyRecord($record, RecordType $recordType, $record_has_inactive = false): array
  {
    $ret_val = ['system_error' => false, 'edit_url' => null];

    // Get base table copy
    /** @var Popup $copied_record */
    $copied_record = $record->replicate(['internal_name', 'cookie_name']);
    $copied_record->internal_name = $record->internal_name . '-' . time();
    $copied_record->cookie_name = $record->cookie_name . '-' . time();

    if($record_has_inactive)
    {
      $copied_record->is_inactive = true;
    }

    $copied_record->save();

    $ret_val['edit_url'] = '/admin/popup/' . $copied_record->id;

    return $ret_val;
  }

  /**
   * Find item from options
   * @param int $product_id
   * @param array $option_values
   * @param int $item_id
   */
  public function findItemFromOptions($product_id, $option_values, $item_id)
  {
    if(!empty($option_values))
    {
      $item = Item::findFromOptions(intval($product_id), $option_values);
    }
    else
    {
      $item = Item::whereRaw('id = ? AND is_inactive = ?', [$item_id, false])->first();
    }

    return $item;
  }

  /**
   * Get item data
   * @param Item $item
   * @return array
   */
  public function getItemData(Item $item): array
  {

  }
}