<?php

namespace App\Http\Middleware;

use Closure;

class HandleMarketingChannelCapture
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request $request
   * @param  \Closure $next
   * @return mixed
   */
  public function handle($request, Closure $next)
  {
    if(!empty($request->getQueryString()) && !empty($request->get(business('marketing_channel_query'))))
    {
      $domain = preg_replace('/(https?\:\/\/)/i', '', business('site_url'));

      setcookie('m_source', $request->get(business('marketing_channel_query')),
        time() + (86400 * 30), '/', $domain, false, true);
    }

    return $next($request);
  }
}
