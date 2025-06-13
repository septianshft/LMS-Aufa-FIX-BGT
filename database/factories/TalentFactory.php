<?php

namespace Database\Factories;

use App\Models\Talent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TalentFactory extends Factory
{
    protected $model = Talent::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'skills' => json_encode(['PHP', 'Laravel', 'JavaScript']),
            'experience_level' => $this->faker->randomElement(['junior', 'mid', 'senior']),
            'portfolio_url' => $this->faker->url(),
            'hourly_rate' => $this->faker->numberBetween(20, 100),
            'availability_status' => $this->faker->randomElement(['available', 'busy', 'unavailable']),
            'bio' => $this->faker->paragraph(),
            'location' => $this->faker->city(),
        ];
    }
}
