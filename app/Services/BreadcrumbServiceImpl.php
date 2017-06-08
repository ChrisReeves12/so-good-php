<?php
/**
 * The BreadcrumbServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\IBreadcrumbService;

/**
 * Class BreadcrumbServiceImpl
 * @package App\Services
 */
class BreadcrumbServiceImpl implements IBreadcrumbService
{
  /**
   * Adds an entry to the breadcrumb stack
   * @param array $entry
   */
  public function add(array $entry)
  {
    $raw_entry = $entry;
    $entry = json_encode($entry);
    $breadcrumb_storage = session('breadcrumbs');

    if(empty($breadcrumb_storage))
      $breadcrumb_storage = [];

    if(!empty($breadcrumb_storage))
    {
      $storage_array_entry = json_decode($breadcrumb_storage[0], true);
      if(key($storage_array_entry) != key($raw_entry))
      {
        if(count($breadcrumb_storage) === 3)
        {
          array_pop($breadcrumb_storage);
        }


        array_unshift($breadcrumb_storage, $entry);
      }

      // Remove duplicate from end
      if(key(json_decode($breadcrumb_storage[0], true)) == key(json_decode(end($breadcrumb_storage), true)))
        array_pop($breadcrumb_storage);
    }
    else
    {
      array_unshift($breadcrumb_storage, $entry);
    }

    session(['breadcrumbs' => $breadcrumb_storage]);
  }
}