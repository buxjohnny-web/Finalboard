<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define a macro to detect mobile user agents
        Request::macro('isMobile', function (): bool {
            /** @var \Illuminate\Http\Request $this */
            $ua = (string) $this->header('User-Agent', '');
            $accept = (string) $this->header('Accept', '');
            $xWapProfile = $this->header('X-WAP-PROFILE');

            if ($xWapProfile) {
                return true;
            }

            // Common mobile indicators in Accept header
            if (stripos($accept, 'application/vnd.wap.xhtml+xml') !== false) {
                return true;
            }

            // Basic user agent pattern for mobile devices
            $pattern = '/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i';

            return (bool) preg_match($pattern, $ua);
        });
    }
}
