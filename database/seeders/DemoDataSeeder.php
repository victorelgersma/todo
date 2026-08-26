<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    protected string $meEmail = 'victorelgersma@fastmail.com';

    public function run(): void
    {
        $me = User::where('email', $this->meEmail)->first()
            ?? User::factory()->create(['email' => $this->meEmail, 'name' => 'Victor']);

        $groceries = $me->todoLists()->create(['name' => 'Groceries', 'position' => 0]);
        $groceries->tasks()->createMany([
            ['title' => 'Oat milk', 'position' => 0],
            ['title' => 'Coffee beans', 'position' => 1],
            ['title' => 'Bin bags', 'position' => 2, 'completed_at' => now()->subDay()],
        ]);

        $work = $me->todoLists()->create(['name' => 'Work', 'position' => 1]);
        $work->tasks()->createMany([
            ['title' => 'Fix the pennypost test suite', 'position' => 0],
            ['title' => 'Reply to KPN about the roaming charge', 'position' => 1],
        ]);

        $someday = $me->todoLists()->create(['name' => 'Someday', 'position' => 2]);
        $someday->tasks()->createMany([
            ['title' => 'Learn to solder', 'position' => 0],
        ]);

        $this->command->info("Demo data seeded for {$me->email}.");
    }
}
