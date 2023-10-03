<?php

namespace Tests\Feature;

use App\Events\OrderStatusChanged;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderStatusChangedUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_manager_can_notify_user_of_order_status_change()
    {
        Event::fake([
            OrderStatusChanged::class,
        ]);
        
        // Create a manager
        $manager = User::factory()->create(['role' => 'MANAGER']);

        // Create a customer
        $customer = User::factory()->create(['role' => 'CUSTOMER']);

        // Authenticate as the manager
        $this->actingAs($manager);

        // Create an order associated with the user
        $order = Order::factory()->create(['user_id' => $customer->id, 'status' => 'WAITING']);

        // Simulate changing the order status
        $newStatus = 'PREPARATION';
        $order->update(['status' => $newStatus]);
        
        Event::assertDispatched(OrderStatusChanged::class);

    }
}
