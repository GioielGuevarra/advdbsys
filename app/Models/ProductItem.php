<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_item_id';
    protected $fillable = [
        'product_id',
        'batch_number',
        'expiration_date',
        'quantity',
        'status',
        'added_at'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}