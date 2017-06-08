<?php
/**
 * The StockLocationServiceImpl class definition.
 *
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Address;
use App\Services\Contracts\ICRUDRecordTypeService;
use App\Services\Contracts\IStockLocationService;
use App\StockLocation;
use Illuminate\Support\Collection;

/**
 * Class StockLocationServiceImpl
 * @package App\Services
 */
class StockLocationServiceImpl implements IStockLocationService, ICRUDRecordTypeService
{
  /**
   * Create or update a stock location
   * @param StockLocation $stock_location
   * @param array $data
   * @return array
   */
  public function createUpdate($stock_location, array $data = [])
  {
    $ret_val = ['system_error' => false, 'errors' => []];

    // Validate address
    $address_errors = [];
    if($stock_location->address instanceof Address)
    {
      $address_is_valid = $stock_location->address->validate($data['data']['address'], $address_errors);
    }
    else
    {
      $address = new Address($data['data']['address']);
      $address_is_valid = $address->validate($data['data']['address'], $address_errors);
    }

    if(!$address_is_valid)
    {
      foreach($address_errors as $address_error)
      {
        $ret_val['errors'][] = ['address.' . key($address_error) => array_values($address_error)];
      }
    }
    else
    {
      if(!empty($address) && !($stock_location->address instanceof Address))
      {
        $address->save();
        $data['data']['address_id'] = $address->id;
      }
      else
      {
        $stock_location->address->update($data['data']['address']);
      }
    }

    // Validate stock location
    $stock_location_errors = [];
    $stock_location_valid = $stock_location->validate($data['data'], $stock_location_errors);
    if(!$stock_location_valid)
    {
      foreach($stock_location_errors as $stock_location_error)
      {
        $ret_val['errors'][] = [key($stock_location_error) => array_values($stock_location_error)];
      }
    }

    if($stock_location_valid && $address_is_valid)
    {
      unset($data['data']['address']);
      unset($data['data']['id']);
      if($stock_location->exists)
      {
        $stock_location->update($data['data']);
      }
      else
      {
        $stock_location->fill($data['data']);
        $stock_location->save();
      }

      $ret_val['id'] = $stock_location->id;
    }

    return $ret_val;
  }

  /**
   * Get all stock locations
   * @return Collection
   */
  public function findAll()
  {
    return StockLocation::all();
  }
}