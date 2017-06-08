<?php

namespace App\Http\Middleware;

use Closure;

class Authenticate
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
    // Authenticate admin panel
    if(preg_match('/admin/i', $request->path()))
    {
      if(empty(current_user()) || current_user('role') !== 'admin')
      {
        return redirect('/sign-in?whence=' . urlencode($request->path()));
      }
    }
    elseif(preg_match('/account/i', $request->path()))
    {
      if(empty(current_user()))
      {
        return redirect('/sign-in?whence=' . urlencode($request->path()));
      }
    }

    return $next($request);
  }
}
