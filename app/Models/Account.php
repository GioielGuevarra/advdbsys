<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'account_id';
    protected $fillable = [
        'email',
        'first_name',
        'last_name', 
        'password',
        'role',
        'is_customer',
        'is_admin',
        'is_staff'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'account_id');
    }

}