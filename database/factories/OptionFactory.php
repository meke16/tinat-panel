<?php

namespace Database\Factories;

use App\Models\Option;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionFactory extends Factory // ✅ Correct class name
{
    protected $model = Option::class;

    public function definition(): array
    {
        return [
            'question_id' => Question::factory(),
             'text' => $this->faker->sentence(10),
            'order_index' => $this->faker->numberBetween(1, 4)
        ];
    }
}
