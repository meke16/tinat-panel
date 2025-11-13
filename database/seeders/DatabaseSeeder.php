<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
         // Example subchapter IDs you’ll pass
        $subchapterIds = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50.51,52,53,54,55,56,57,58,59,60];

        // Create 20 random questions
        $questions = Question::factory()
            ->count(200)
            ->state(fn() => ['subchapter_id' => fake()->randomElement($subchapterIds)])
            ->create();

        // For each question, create 4 options (1 correct)
        $questions->each(function ($question) {
            $correctIndex = rand(1, 4);
            foreach (range(1, 4) as $i) {
                Option::factory()->create([
                    'question_id' => $question->id,
                    'text' => [
                        'value' => fake()->sentence(3),
                        'is_correct' => $i === $correctIndex,
                    ],
                    'order_index' => $i,
                ]);
            }
        });
    }
}
