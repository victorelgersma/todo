<?php

use App\Models\Task;
use App\Models\User;

test('a user can add a task to their own list', function () {
    $user = User::factory()->create();
    $list = $user->todoLists()->create(['name' => 'Groceries', 'position' => 0]);

    $response = $this->actingAs($user)->post("/lists/{$list->id}/tasks", ['title' => 'Oat milk']);

    $response->assertRedirect(route('lists.show', $list));
    $task = Task::first();
    expect($task->title)->toBe('Oat milk');
    expect($task->todo_list_id)->toBe($list->id);
    expect($task->completed_at)->toBeNull();
});

test('a user cannot add a task to someone elses list', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $list = $owner->todoLists()->create(['name' => 'Private', 'position' => 0]);

    $this->actingAs($intruder)->post("/lists/{$list->id}/tasks", ['title' => 'Sneaky'])->assertNotFound();

    $this->assertDatabaseMissing('tasks', ['title' => 'Sneaky']);
});

test('toggling a task marks it complete, and toggling again reopens it', function () {
    $user = User::factory()->create();
    $list = $user->todoLists()->create(['name' => 'Groceries', 'position' => 0]);
    $task = $list->tasks()->create(['title' => 'Oat milk', 'position' => 0]);

    $this->actingAs($user)->patch("/tasks/{$task->id}/toggle");
    expect($task->fresh()->completed_at)->not->toBeNull();

    $this->actingAs($user)->patch("/tasks/{$task->id}/toggle");
    expect($task->fresh()->completed_at)->toBeNull();
});

test('a user can edit a tasks title', function () {
    $user = User::factory()->create();
    $list = $user->todoLists()->create(['name' => 'Groceries', 'position' => 0]);
    $task = $list->tasks()->create(['title' => 'Oat milk', 'position' => 0]);

    $response = $this->actingAs($user)->patch("/tasks/{$task->id}", ['title' => 'Oat milk (the good kind)']);

    $response->assertRedirect(route('lists.show', $list));
    expect($task->fresh()->title)->toBe('Oat milk (the good kind)');
});

test('a user can delete a task', function () {
    $user = User::factory()->create();
    $list = $user->todoLists()->create(['name' => 'Groceries', 'position' => 0]);
    $task = $list->tasks()->create(['title' => 'Oat milk', 'position' => 0]);

    $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

    $response->assertRedirect(route('lists.show', $list));
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('a user cannot toggle, edit, or delete a task on someone elses list', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $list = $owner->todoLists()->create(['name' => 'Private', 'position' => 0]);
    $task = $list->tasks()->create(['title' => 'Not yours', 'position' => 0]);

    $this->actingAs($intruder)->patch("/tasks/{$task->id}/toggle")->assertNotFound();
    $this->actingAs($intruder)->patch("/tasks/{$task->id}", ['title' => 'Hijacked'])->assertNotFound();
    $this->actingAs($intruder)->delete("/tasks/{$task->id}")->assertNotFound();

    expect($task->fresh()->title)->toBe('Not yours');
    expect($task->fresh()->completed_at)->toBeNull();
});

test('open and completed tasks are split correctly on the list show page', function () {
    $user = User::factory()->create();
    $list = $user->todoLists()->create(['name' => 'Groceries', 'position' => 0]);
    $list->tasks()->create(['title' => 'Still to buy', 'position' => 0]);
    $list->tasks()->create(['title' => 'Already bought', 'position' => 1, 'completed_at' => now()]);

    $response = $this->actingAs($user)->get(route('lists.show', $list));

    $response->assertOk();
    expect($response->viewData('openTasks')->pluck('title')->all())->toBe(['Still to buy']);
    expect($response->viewData('completedTasks')->pluck('title')->all())->toBe(['Already bought']);
});
