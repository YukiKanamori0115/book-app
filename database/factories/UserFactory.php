<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'employee_id' => $this->faker->unique()->numerify('EMP####'),
            'name' => fake()->name(),
            'password' => Hash::make('password'),
            // role_id用に適当なロールIDを割り当てるか、存在するロールIDを指定
            'role_id' => 1, 
        ];
    }
}