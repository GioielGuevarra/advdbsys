<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $dates = ['deleted_at'];

    // These fields will be nulled on soft delete
    protected $nullableOnDelete = [
        'email',
        'password',
        'remember_token',
    ];

    public function delete()
    {
        // Null out sensitive information
        foreach ($this->nullableOnDelete as $field) {
            $this->$field = null;
        }
        $this->save();

        // Perform soft delete
        return parent::delete();
    }

    public function account()
    {
        return $this->hasOne(Account::class, 'email', 'email');
    }

    public function getNameAttribute()
    {
        return $this->account ? $this->account->first_name . ' ' . $this->account->last_name : '';
    }

    public function isAdmin(): bool
    {
        return $this->account !== null && $this->account->is_admin === true;
    }

    public function isStaff(): bool
    {
        return $this->account !== null && $this->account->is_staff === true;
    }

    public function hasAdminAccess(): bool
    {
        return $this->account !== null && ($this->account->is_admin === true || $this->account->is_staff === true);
    }
}