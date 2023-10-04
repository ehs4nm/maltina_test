<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Notifications\OrderStatusChangedNotification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOrderStatusChangedNotification
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
    public function handle(OrderStatusChanged $event): void
    {
        //we can change or do anything with the status or order then pass it to the notification class
        $order = $event->order;

        // Notify the user about the order status change
        try {
            $order->user->notify(new OrderStatusChangedNotification($order));
        }
        catch (Exception $e) {
            // Log a custom message along with the exception
            Log::error('An exception occurred while notifying the user: ' . $e->getMessage());
            return;
        }
    }
}
