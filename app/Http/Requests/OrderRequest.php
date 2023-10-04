<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'nullable|exists:users,id', // Ensure the user_id exists in the 'users' table.
            // 'total_price' => 'nullable|integer|min:0|max:999999', // user should not set the total price, it should be calculated by app
            // 'status' => 'nullable|in:WAITING,PREPARATION,READY,DELIVERED', // user should not set the status, it should be calculated by app
            'consume_location' => 'required|in:TAKE_AWAY,IN_SHOP',
            'products' => 'required|array', // Ensure 'products' is an array
            'products.*.product_id' => 'required|exists:products,id', // Validate each product_id
            'products.*.option_id' => 'required|exists:options,id', // Validate each option_id
            'products.*.quantity' => 'required|integer|min:1|max:100', // Validate quantity for each product
        ];
    }
}
