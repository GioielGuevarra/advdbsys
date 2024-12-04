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

    public static function deductStock(Product $product, $quantityToDeduct)
    {
        $availableItems = self::where('product_id', $product->product_id)
            ->where('status', 'in_stock')
            ->where('quantity', '>', 0)
            ->orderBy('expiration_date', 'asc')  // Use FIFO (First In, First Out)
            ->get();

        $remainingDeduction = $quantityToDeduct;

        foreach ($availableItems as $item) {
            if ($remainingDeduction <= 0) break;

            $deduction = min($item->quantity, $remainingDeduction);
            $item->quantity -= $deduction;
            $remainingDeduction -= $deduction;

            if ($item->quantity == 0) {
                $item->status = 'out_of_stock';
            }

            $item->save();
        }

        if ($remainingDeduction > 0) {
            throw new \Exception("Insufficient stock for product: {$product->product_name}");
        }
    }
}