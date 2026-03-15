<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        $positions = [
            'Software Engineer',
            'Senior Software Engineer',
            'Product Manager',
            'HR Manager',
            'Data Analyst',
            'DevOps Engineer',
            'UI/UX Designer',
            'QA Engineer',
            'Backend Developer',
            'Frontend Developer',
        ];

        return [
            'title'       => $this->faker->unique()->randomElement($positions),
            'description' => $this->faker->sentence(),
        ];
    }
}