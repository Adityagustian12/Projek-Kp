<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Bill;
use App\Models\Booking;
use App\Models\Complaint;
use App\Policies\BillPolicy;
use App\Policies\BookingPolicy;
use App\Policies\ComplaintPolicy;
use App\Observers\BookingObserver;

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
        Gate::policy(Bill::class, BillPolicy::class);
        Gate::policy(Booking::class, BookingPolicy::class);
        Gate::policy(Complaint::class, ComplaintPolicy::class);
        
        // Register Booking Observer for automatic room status sync
        Booking::observe(BookingObserver::class);
    }
}
