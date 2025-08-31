<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition()
    {
        return [
            'driver_id'    => $this->faker->unique()->numerify('DRV###'),
            'full_name'    => $this->faker->name(),
            'phone_number' => $this->faker->phoneNumber(),
            'active'       => $this->faker->boolean(80),
            'added_by'     => User::inRandomOrder()->first()->id,
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}