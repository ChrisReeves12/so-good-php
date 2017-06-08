<?php
/**
 * The ReportServiceImpl class definition.
 *
 * Default ReportService implementation
 * @author Christopher Lee Reeves <ChrisReeves12@yahoo.com>
 **/

namespace App\Services;

use App\Services\Contracts\IReportService;
use App\Entity;
use App\ShoppingCart;
use App\ShoppingCartDatum;
use App\Subscription;
use App\TransactionLineItem;
use DB;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * Class ReportServiceImpl
 * @package App\Services
 */
class ReportServiceImpl implements IReportService
{
  private $day_start;
  private $day_end;

  /**
   * ReportServiceImpl constructor.
   */
  public function __construct()
  {
    $this->day_start = Carbon::now(business('timezone'))->startOfDay()->timezone('UTC');
    $this->day_end = Carbon::now(business('timezone'))->endOfDay()->timezone('UTC');
  }

  /**
   * Gets sales order data for display on Admin home for today's orders
   * @return Collection
   */
  public function getAdminHomeTodayOrders(): Collection
  {
    return DB::table('sales_orders')
      ->select('transactions.*', 'sales_orders.id as sales_order_id', 'sales_orders.status', 'sales_orders.created_at as sales_order_created_at', 'sales_orders.marketing_channel')
      ->join('transactions', 'transactions.id', '=', 'sales_orders.transaction_id')
      ->orderBy('sales_orders.created_at', 'desc')
      ->whereBetween('sales_orders.created_at', [$this->day_start, $this->day_end])->get();
  }

  /**
   * Get total store revenue in sales
   * @return float
   */
  public function getTotalStoreRevenue()
  {
    return DB::table('sales_orders')
      ->join('transactions', 'transactions.id', '=', 'sales_orders.transaction_id')
      ->where('sales_orders.status', '<>', 'canceled')
      ->sum('transactions.sub_total');
  }

  /**
   * Get total revenue within timeframe
   * @param $begin_time
   * @param $end_time
   * @return float
   */
  public function getTotalStoreRevenueInScope($begin_time, $end_time)
  {
    return DB::table('sales_orders')
      ->join('transactions', 'transactions.id', '=', 'sales_orders.transaction_id')
      ->where('sales_orders.status', '<>', 'canceled')
      ->whereBetween('sales_orders.created_at', [$begin_time, $end_time])
      ->sum('transactions.sub_total');
  }

  /**
   * Get orders in pending status
   * @return Collection
   */
  public function geAdminHometPendingOrders(): Collection
  {
    return DB::table('sales_orders')
      ->select('transactions.*', 'sales_orders.id as sales_order_id', 'sales_orders.status', 'sales_orders.created_at as sales_order_created_at', 'sales_orders.marketing_channel')
      ->join('transactions', 'transactions.id', '=', 'sales_orders.transaction_id')
      ->whereNotIn('sales_orders.status', ['canceled', 'shipped'])
      ->orderBy('sales_orders.created_at', 'desc')
      ->get();
  }

  /**
   * Get today's users for admin home page
   * @return Collection
   */
  public function getAdminHomeTodayUsers(): Collection
  {
    return DB::table('entities')
      ->whereBetween('entities.created_at', [$this->day_start, $this->day_end])->get();
  }

  /**
   * Generates a CSV output of the type of email subscriber report
   * @param string $type
   * @return string
   */
  public function generateEmailSubReport(string $type)
  {
    $csv_output = '';

    // Generate correct report
    switch($type)
    {
      // Gets newsletter subscribers
      case 'newsletter':
        $csv_output = "email_address,date_added\n";
        $subs = Subscription::where('is_inactive', false)->orderBy('created_at', 'desc')->get();

        /** @var Subscription $sub */
        foreach($subs as $sub)
        {
          $csv_output .= "{$sub->email},{$sub->created_at->format('m/d/Y')}\n";
        }

        break;

      // Get emails from sales orders
      case 'sales-orders':
        $csv_output = "first_name,last_name,email_address,last_order_date\n";
        $entries = DB::table('transactions')
          ->select(DB::Raw('DISTINCT(transactions.email), transactions.created_at, transactions.created_at, transactions.first_name, transactions.last_name'))
          ->join('sales_orders', 'sales_orders.transaction_id', '=', 'transactions.id')
          ->orderBy('transactions.created_at', 'desc')
          ->get();

        foreach($entries as $entry)
        {
          $last_ordered = (new \DateTime($entry->created_at))->format('m/d/Y');
          $csv_output .= "{$entry->first_name},{$entry->last_name},{$entry->email},{$last_ordered}\n";
        }
        break;

      // Get users emails
      case 'users':
        $csv_output = "first_name,last_name,email_address,date_joined\n";
        $entities = Entity::orderBy('created_at', 'desc')->get();

        /** @var Entity $entity */
        foreach($entities as $entity)
        {
          $date_joined = $entity->created_at->format('m/d/Y');
          $csv_output .= "{$entity->first_name},{$entity->last_name},{$entity->email},{$date_joined}\n";
        }
        break;
    }

    return $csv_output;
  }

  /**
   * Generate data for recent open shopping carts
   * @param int $min_hours
   * @param int $max_hours
   * @param Command $command
   */
  public function generateRecentCartData(int $min_hours, int $max_hours, Command $command)
  {
    // Clean up old data
    $command->info('Cleaning up old data...');
    $all_shopping_data = ShoppingCartDatum::all();

    /** @var ShoppingCartDatum $cart_datum */
    foreach($all_shopping_data as $cart_datum)
    {
      if($cart_datum->created_at->diffInHours(Carbon::now()) > $max_hours)
        $cart_datum->delete();
    }

    // Get shopping carts
    $carts = ShoppingCart::all();

    /** @var ShoppingCart $cart */
    foreach($carts as $cart)
    {
      if(empty($cart->email))
        continue;

      // Check if there are other carts under this email
      $carts_under_this_email = ShoppingCart::join('transactions', 'transactions.id', '=', 'shopping_carts.transaction_id')
        ->whereRaw('lower(transactions.email) = lower(?)', [$cart->email])
        ->orderBy('shopping_carts.updated_at', 'desc')->get();

      if($carts_under_this_email->count() > 1)
      {
        // Choose the most recent
        $cart = $carts_under_this_email->first();
      }

      if($cart->created_at->diffInHours(Carbon::now()) > $min_hours && $cart->created_at->diffInHours(Carbon::now()) < $max_hours)
      {
        // Check if shopping cart data for this cart already exists
        if(ShoppingCartDatum::where('shopping_cart_id', $cart->id)->count() == 0)
        {
          $command->line('Adding cart ID: ' . $cart->id . ' for email: ' . $cart->email . ' to data...');

          $line_item_data = [];
          $datum = new ShoppingCartDatum();
          $datum->email = $cart->email;
          $datum->shopping_cart_id = $cart->id;
          $datum->parsed = false;

          /** @var TransactionLineItem $line_item */
          foreach($cart->line_items as $line_item)
          {
            $line_item_data[] = [
              'id' => $line_item->id,
              'product_id' => $line_item->item->product->id,
              'sku' => $line_item->item->sku,
              'product_name' => $line_item->item->product->name,
              'details' => $line_item->details_for_checkout,
              'image' => $line_item->image_url,
              'quantity' => $line_item->quantity,
              'unit_price' => $line_item->unit_price,
              'sub_total' => $line_item->sub_total
            ];
          }

          $datum->line_items = $line_item_data;
          $datum->save();
        }
      }
    }

    $command->info('Complete!');
  }
}