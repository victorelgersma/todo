<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TodoList;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'todo_list_id' => TodoList::factory(),
            'title' => fake()->sentence(4),
            'position' => 0,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => ['completed_at' => now()]);
    }
}
