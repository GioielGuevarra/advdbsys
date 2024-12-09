<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

    // Add status constants for cleaner code
    const STATUS_PENDING = 'pending';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY = 'ready';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Helper method to check if order can be marked as completed
    public function canComplete()
    {
        return $this->status === self::STATUS_READY;
    }

    // Helper method to check if order status can be changed
    public function canChangeStatus()
    {
        return !in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED]);
    }

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

    protected function getPickupTimeAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Manila');
    }

    protected function setPickupTimeAttribute($value)
    {
        $this->attributes['pickup_time'] = Carbon::parse($value)->timezone('Asia/Manila');
    }
}