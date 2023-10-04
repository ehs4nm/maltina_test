<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    private function transformProducts($products)
    {
        return $products->map(function ($product) {
            return [
                'product_id' => $product->pivot->product_id,
                'quantity' => $product->pivot->quantity,
                'sum_price' => $product->pivot->sum_price,
                'option_id' => $product->pivot->option_id,
            ];
        });
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'total_price' => $this->total_price,
            'consume_location' => $this->consume_location,
            'user_id' => $this->user_id, // it should be hidden too, not letting the consumer know the id for users
            'products' => $this->transformProducts($this->cart->products),
            // 'products' => ProductResource::collection($this->whenLoaded('cart.products')),
        ];
    }
}
