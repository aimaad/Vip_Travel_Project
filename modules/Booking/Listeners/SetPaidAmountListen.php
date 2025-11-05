<?php


namespace Modules\Booking\Listeners;

use App\Notifications\AdminChannelServices;
use App\Notifications\PrivateChannelServices;
use App\User;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Events\BookingCreatedEvent;
use Modules\Booking\Events\SetPaidAmountEvent;

class SetPaidAmountListen
{
    public function handle(SetPaidAmountEvent $event)
    {
        $booking = $event->booking;
        $vendor = $booking->vendor()->where('status', 'publish')->first();
        $user = Auth::user();

        $data = [
            'event'   => 'SetPaidAmountEvent',
            'to'      => 'admin',
            'id'      => $booking->id,
            'name'    => $user ? $user->name : 'Nom inconnu', // Utilisation du champ 'name'
            'avatar'  => $user ? $user->avatar_url : null,
            'link'    => route('report.admin.booking'),
            'type'    => $booking->object_model,
            'message' => __(':name has updated the PAID amount on :title', [
                'name' => $vendor ? $vendor->name : 'Nom inconnu', // Utilisation du champ 'name' pour le vendor
                'title' => $booking->service->title
            ]),
        ];

        if ($user) {
            $user->notify(new AdminChannelServices($data));
        }

        // Notify vendor
        if ($vendor) {
            if (!$vendor->hasPermission('dashboard_access')) {
                $data['to'] = 'vendor';
                $data['link'] = route("vendor.bookingReport");
                $data['message'] = __('Administrator has updated the PAID amount on :title', ['title' => $booking->service->title]);
                $vendor->notify(new PrivateChannelServices($data));
            }
        }

        $customer = User::where('id', $booking->customer_id)->where('status', 'publish')->first();

        if ($customer) {
            if (!$customer->hasPermission('dashboard_access')) {
                $data['to'] = 'customer';
                $data['link'] = route('user.booking_history');
                $customer->notify(new PrivateChannelServices($data));
            }
        }
    }
}
