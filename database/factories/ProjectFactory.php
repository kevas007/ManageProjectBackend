<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $states = State::all();
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'state_id' => $states->random()->id,
            'deadline' => fake()->dateTime()
        ];
    }
}
