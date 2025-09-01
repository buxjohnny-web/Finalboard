<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Check if the session has a stored locale
        if (Session::has('locale')) {
            $locale = Session::get('locale');

            // 2. Validate that the locale is in our list of supported languages
            if (in_array($locale, config('app.available_locales'))) {
                
                // 3. Set the application's locale for the current request
                App::setLocale($locale);

                // 4. Set the locale for the Carbon date library
                Carbon::setLocale($locale);
            }
        }
        
        // If no valid locale is found in the session, Laravel will use the
        // default locale set in 'config/app.php'
        return $next($request);
    }
}