<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'price'];

    // In boot method we create a slug from the name 
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name, '-');
        });

        static::updating(function ($model) {
            $model->slug = Str::slug($model->name, '-');
        });
    }

    // Set the route ky for product controller
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
}
