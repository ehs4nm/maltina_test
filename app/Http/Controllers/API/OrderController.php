<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

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
     * Store a newly created order in storage.
     *
     * @param  App\Http\Requests\OrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(OrderRequest $request)
    {
        // Store a newly created order in storage.
        // Validate the incoming request data thourogh OrderRequest and Retrieve the validated input data.
        $validatedOrderData = $request->validated();
        
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
     * Update the specified order in storage.
     *
     * @param  App\Http\Requests\OrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(OrderRequest $request, Order $order)
    {
        // Validate the incoming request data.
        // Validate the incoming request data thourogh OrderRequest and Retrieve the validated input data.
        $validatedUpdateOrderData = $request->validated();

        if(auth()->user()->role === 'CUSTOMER' && $order->status === 'WAITING') {
            $order->update($validatedUpdateOrderData); // Update the specified order if user is a customer and order is waiting.
            return response()->json(null, 200); // Return a JSON response with a success message and HTTP status code OK.
        }
        
        if(auth()->user()->role === 'MANAGER') {
            $order->update($validatedUpdateOrderData); // Update the specified order if user is a manager.
            return response()->json(null, 200); // Return a JSON response with a success message and HTTP status code OK.
        }

        return response()->json(null, 403);
    }

    /**
     * Remove the specified order from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        if(auth()->user()->role === 'CUSTOMER' && $order->status === 'WAITING') {
            $order->delete(); // Delete the specified order from the database if user is a customer and order is waiting.
            return response()->json(null, 204); // Return a JSON response with a success message and HTTP status code 204 (No Content).
        }
        
        if(auth()->user()->role === 'MANAGER') {
            $order->delete(); // Delete the specified order from the database if user is a manager.
            return response()->json(null, 204); // Return a JSON response with a success message and HTTP status code 204 (No Content).
        }

        return response()->json(null, 403);
    }
}
