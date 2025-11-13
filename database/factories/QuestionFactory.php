<?php

namespace Database\Factories;

use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        // Replace this array with your real subchapter IDs
        $subchapterIds = [1, 2, 3, 4, 5];

        return [
            'subchapter_id' => $this->faker->randomElement($subchapterIds),
            'question_text' => $this->faker->sentence(10),
            'explanation' => $this->faker->paragraph(),
            'answerIndex' => $this->faker->numberBetween(0, 5),
            'tags' => $this->faker->words(5, true)
        ];
    }
}
