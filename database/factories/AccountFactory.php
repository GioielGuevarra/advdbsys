<?php
namespace Database\Factories;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition()
    {
        $roles = ['staff', 'admin'];
        $role = $this->faker->randomElement($roles);
        
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'password' => Hash::make('password'), // Default password for testing
            'role' => $role,
            'is_customer' => false,
            'is_admin' => $role === 'admin',
            'is_staff' => $role === 'staff',
        ];
    }
}