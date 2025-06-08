<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = \App\Models\Course::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug(),
            'about' => $this->faker->paragraph(),
            'path_trailer' => 'abcd',
            'thumbnail' => 'thumb.png',
            'trainer_id' => \App\Models\Trainer::factory(),
            'category_id' => \App\Models\Category::factory(),
            'price' => 0,
            'course_mode_id' => null,
            'course_level_id' => null,
        ];
    }
}
