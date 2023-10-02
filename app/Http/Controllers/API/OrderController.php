<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve all orders
        $orders = Order::all();

        // Return a JSON response with the list of orders and HTTP status code 200 (OK)
        return response()->json($orders, 200);
    }

    /**
     * Show the form for creating a new order.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Show the form for creating a new order (not implemented).
    }

    /**
     * Store a newly created order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Store a newly created order in storage.
        // Validate the incoming request data.
        $validatedOrderData = $request->validate([
            'user_id' => 'required|exists:users,id', // Ensure the user_id exists in the 'users' table.
            'total_price' => 'required|integer|min:0|max:999999',
            'status' => 'required|in:WAITING,PREPARATION,READY,DELIVERED',
            'consume_location' => 'required|in:TAKE_AWAY,IN_SHOP',
        ]);

        // Create and store the new order using the validated data.
        $order = Order::create($validatedOrderData);

        // Return a JSON response with the created order and HTTP status code 201 (Created).
        return response()->json($order, 201);
    }

    /**
     * Display the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        // Return a JSON response with the specified order and HTTP status code 200 (OK).
        return response()->json($order, 200);
    }

    /**
     * Show the form for editing the specified order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        // Show the form for editing the specified order (not implemented).
    }

    /**
     * Update the specified order in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        // Validate the incoming request data.
        $validUpdatedOrder = $request->validate([
            'total_price' => 'required|integer|min:0|max:999999',
            'status' => 'required|in:WAITING,PREPARATION,READY,DELIVERED',
            'consume_location' => 'required|in:TAKE_AWAY,IN_SHOP',
        ]);

        // Update the specified order with the validated data.
        $order->update($validUpdatedOrder);

        // Return a JSON response with the updated order and HTTP status code 200 (OK).
        return response()->json($order, 200);
    }

    /**
     * Remove the specified order from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        // Delete the specified order from the database.
        $order->delete();

        // Return a JSON response with a success message and HTTP status code 204 (No Content).
        return response()->json(null, 204);
    }
}
