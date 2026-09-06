<?php

namespace App\Providers;

use App\Contracts\Files\FileStorage;
use App\Contracts\Payments\PaymentGateway;
use App\Services\Files\PrivateFileStorage;
use App\Services\Payments\CassoPaymentGateway;
use App\Support\AuditLogger;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FileStorage::class, PrivateFileStorage::class);
        $this->app->bind(PaymentGateway::class, CassoPaymentGateway::class);
        $this->app->singleton(AuditLogger::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Auth rate limits — intentionally tight to resist brute force
        RateLimiter::for('auth-login', static fn (Request $request): Limit => Limit::perMinute(10)->by('auth-login|'.$request->ip()));
        RateLimiter::for('auth-register', static fn (Request $request): Limit => Limit::perMinute(10)->by('auth-register|'.$request->ip()));
        RateLimiter::for('auth-verify-otp', static fn (Request $request): Limit => Limit::perMinute(10)->by('auth-verify-otp|'.$request->ip()));

        RateLimiter::for('password-otp', static fn (Request $request): Limit => Limit::perMinutes(5, 10)->by(
            'password-otp|'.($request->user('api')?->getKey() ?? $request->ip())
        ));
        RateLimiter::for('citizen-password-verify', static fn (Request $request): Limit => Limit::perMinute(10)->by(
            'citizen-password-verify|'.($request->user('api')?->getKey() ?? $request->ip())
        ));

        RateLimiter::for('chat', static fn (Request $request): Limit => Limit::perMinute(20)->by(
            'chat|'.$request->ip(),
        ));
        RateLimiter::for('public-application-tracking', static function (Request $request): array {
            $code = Str::upper((string) preg_replace('/\s+/', '', trim((string) $request->input('code'))));

            return [
                Limit::perMinute(20)->by('public-application-tracking-ip|'.$request->ip()),
                Limit::perMinute(5)->by('public-application-tracking-code|'.$code),
            ];
        });
        RateLimiter::for('citizen-submissions', static fn (Request $request): Limit => Limit::perMinute(15)->by(
            'citizen-submissions|'.($request->user('api')?->getKey() ?? $request->ip())
        ));
    }
}
