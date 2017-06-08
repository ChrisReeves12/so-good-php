<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
  public function boot()
  {
    /**
     * Validate numeric if there is a value
     */
    Validator::extend('numeric_if_exists', function ($attribute, $value)
    {
      if (empty($value))
      {
        return true;
      }
      else
      {
        $validator = Validator::make([$attribute => $value], [$attribute => 'numeric']);

        return !$validator->fails();
      }
    });

    /**
     * Validate unique if it exists
     */
    Validator::extend('unique_if_exists', function ($attribute, $value, $parameters)
    {
      if (empty($value))
      {
        return true;
      }
      else
      {
        $validator = Validator::make([$attribute => $value], [$attribute => 'unique:' . implode(',', $parameters)]);

        return !$validator->fails();
      }
    });
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }
}
