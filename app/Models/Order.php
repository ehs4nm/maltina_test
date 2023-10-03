<?php

namespace App\Models;

use App\Events\OrderStatusChanged;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['total_price', 'status', 'consume_location', 'user_id'];
    protected $oldStatus;
    // In boot method we fire OrderStatusChanged when order status changes
    public static function boot()
    {
        parent::boot();

        static::updated(function ($order) {
            event(new OrderStatusChanged($order));
        });
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
