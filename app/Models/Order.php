<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_id';
    protected $fillable = [
        'account_id',
        'customer_name',
        'status',
        'total_price',
        'order_number',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}