<?php
/**
 * Extra helper functions
 */

if(!function_exists('business'))
{
  function business(string $key)
  {
    return (!isset(config('business')[$key])) ? null : config('business')[$key];
  }
}

if(!function_exists('service_config'))
{
  function service_config(string $key)
  {
    return (!isset(config('serviceconfig')[$key])) ? null : config('serviceconfig')[$key];
  }
}

if(!function_exists('layout_assets'))
{
  function layout_assets(string $file, $prefix = null)
  {
    // Ignore GIF files
    if(preg_match('/\.gif/i', $file))
      $prefix = null;

    return (($prefix ?? '') . '/assets/img/layout/frontend/' . $file);
  }
}

if(!function_exists('current_date'))
{
  function current_date(string $format)
  {
    return with(new \DateTime)->format($format);
  }
}

if(!function_exists('money'))
{
  function money($value)
  {
    return '$' . number_format($value, 2, '.', ',');
  }
}

if(!function_exists('current_user'))
{
  function current_user($key = null)
  {
    $ret_val = null;
    $current_user = session('current_user');
    if(!empty($key))
    {
      $ret_val = $current_user[$key] ?? null;
    }
    else
    {
      $ret_val = $current_user;
    }

    return $ret_val;
  }
}

if(!function_exists('breadcrumbs'))
{
  function breadcrumbs()
  {
    $ret_val = '';
    $breadcrumb_storage = session('breadcrumbs');

    if(!empty($breadcrumb_storage) && is_array($breadcrumb_storage))
    {
      $breadcrumb_storage = array_reverse($breadcrumb_storage);

      $ret_val = "<ol class='breadcrumb'>";
      foreach($breadcrumb_storage as $breadcrumb)
      {
        $data = json_decode($breadcrumb, true);
        if(current($data) != Request::getRequestUri())
          $ret_val .= "<li><a href='".current($data)."'>".key($data)."</a></li>";
        else
          $ret_val .= "<li>".key($data)."</li>";
      }
      $ret_val .= "</ol>";
    }

    return $ret_val;
  }
}

if(!function_exists('tracking_link'))
{
  function tracking_link($carrier, $tracking_number)
  {
    $tracking_number = preg_replace('/\s+/i', '', $tracking_number);
    if(preg_match('/^fedex$/i', $carrier))
      return("https://www.fedex.com/apps/fedextrack/?action=track&trackingnumber={$tracking_number}&cntry_code=us");
    elseif(preg_match('/^ups$/i', $carrier))
      return("https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums={$tracking_number}");
    elseif(preg_match('/^usps$/i', $carrier))
      return("https://tools.usps.com/go/TrackConfirmAction.action?tRef=fullpage&tLabels={$tracking_number}");
  }
}

if(!function_exists('human_time'))
{
  function human_time($date_or_string)
  {
    $format = 'm/d/Y - g:i a';
    if($date_or_string instanceof \DateTime)
    {
      $date_or_string->setTimezone(new DateTimeZone(business('timezone')));
      return $date_or_string->format($format);
    }
    elseif(is_string($date_or_string))
    {
      $date_time = new \DateTime($date_or_string);
      $date_time->setTimezone(new DateTimeZone(business('timezone')));
      return $date_time->format($format);
    }
  }
}