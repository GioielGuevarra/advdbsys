<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';

    protected $fillable = [
        'order_number',
        'account_id', 
        'total_amount',
        'pickup_time',
        'note',
        'status'
    ];

    protected $casts = [
        'pickup_time' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        // Auto-generate order number when creating new order
        static::creating(function ($order) {
            $order->order_number = 'ORD-' . strtoupper(Str::random(8));
        });
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'order_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }
}