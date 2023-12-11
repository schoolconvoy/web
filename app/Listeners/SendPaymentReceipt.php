<?php

namespace App\Listeners;

use App\Events\PaymentReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\InvoicePaid;
use Illuminate\Support\Facades\Log;

class SendPaymentReceipt implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaymentReceived $event): void
    {
        $parent = $event->ward->parent[0];

        $parent->notify(new InvoicePaid($event));
    }
}
