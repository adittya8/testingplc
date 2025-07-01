<?php

namespace Database\Factories;

use App\Models\Luminary;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Luminary>
 */
class LuminaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'node_id' => $this->faker->numberBetween(10203, 93877),
            'lamp_type_id' => 1,
            'concentrator_id' => 1,
            'sub_group_id' => 1,
            'luminary_type_id' => 1,
            'control_gear_type_id' => 1,
            'pole_id' => 1,
            'installation_status' => 1,
            'created_at' => $this->faker->dateTimeBetween('-30 days'),
        ];
    }
}
