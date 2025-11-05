<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Booking\Events\EnquirySendEvent;
use Modules\Booking\Listeners\EnquiryNotifyListen;
use Modules\Booking\Listeners\EnquirySendListen;
use Modules\User\Events\NewVendorRegistered;
use Modules\User\Events\SendMailUserRegistered;
use Modules\User\Events\VendorApproved;
use Modules\User\Listeners\SendMailUserRegisteredListen;
use Modules\User\Listeners\SendNotifyApproved;
use Modules\User\Listeners\SendNotifyRegistered;
use Modules\User\Listeners\SendNotifyRegisteredListen;
use Modules\User\Listeners\SendVendorApprovedMail;
use Modules\User\Listeners\SendVendorRegisterdEmail;
use Modules\Vendor\Events\PayoutRequestEvent;
use Modules\Vendor\Listeners\PayoutNotifyListener;
use Modules\Vendor\Listeners\PayoutRequestNotificationListener;
use App\Events\NewRoleRequestSubmitted;
use App\Listeners\SendRoleRequestNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SendMailUserRegistered::class => [
            SendMailUserRegisteredListen::class,
            SendNotifyRegisteredListen::class
        ],
        \App\Events\OfferConfirmed::class => [
            \App\Listeners\SendOfferConfirmedNotification::class,
        ],
        RoleRequestUpdated::class => [
            SendRoleRequestStatusNotification::class,
        ],
        VendorApproved::class=>[
            SendVendorApprovedMail::class,
            SendNotifyApproved::class
        ],
        NewVendorRegistered::class=>[
            SendVendorRegisterdEmail::class,
            SendNotifyRegistered::class
        ],
//        VendorLogPayment::class =>[
//            VendorLogPaymentListen::class
//        ]
        PayoutRequestEvent::class=>[
            PayoutRequestNotificationListener::class,
            PayoutNotifyListener::class
        ],
        EnquirySendEvent::class=>[
            EnquirySendListen::class,
            EnquiryNotifyListen::class
        ],
        NewRoleRequestSubmitted::class => [
            SendRoleRequestNotification::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
