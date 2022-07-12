<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Models\Role;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => $this->faker->randomElement([Gender::male, Gender::female]),
            'age' => $this->faker->numberBetween(18, 50),
            'phone' => $this->faker->phoneNumber(),
            'photo' => $this->faker->imageUrl(1024, 1024, 'person'),
            'team_id' => Team::query()->inRandomOrder()->value('id'),
            'role_id' => Role::query()->inRandomOrder()->value('id'),
            'verified_at' => $this->faker->randomElement([now(), null]),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'verified_at' => null,
            ];
        });
    }
}
