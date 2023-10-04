<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Option;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    private $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->authorizeResource(Order::class, 'order');
        // Inject the OrderService into OrderController
        $this->orderService = $orderService;
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
        $order = $this->orderService->createOrder($validatedOrderData);

        // Return a JSON response with the created order and HTTP status code 201 (Created).
        return response()->json(new OrderResource($order), 201);
        
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

        // Update the specified order using the OrderService.
        $updatedOrder = $this->orderService->updateOrder($order, $validatedUpdateOrderData);

        if($updatedOrder !== null)
            // Return a JSON response with a success message and HTTP status code OK.
            return response()->json(null, 200); 
        else 
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
        // Delete the specified order using the OrderService.
        $orderSoftDeleted = $this->orderService->deleteOrder($order);
        
        if($orderSoftDeleted === true)
            // Return a JSON response with a success message and HTTP status code 204 (No Content).
            return response()->json(null, 204); 
        else
            // Return a JSON response forbidden.
            return response()->json(null, 403);
    }
}
