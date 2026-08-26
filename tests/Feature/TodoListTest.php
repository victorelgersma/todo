<?php

use App\Models\TodoList;
use App\Models\User;

test('a user with no lists sees the empty state', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/lists')
        ->assertOk()
        ->assertSee("don't have any lists yet");
});

test('a user with lists is redirected to their first list', function () {
    $user = User::factory()->create();
    $list = $user->todoLists()->create(['name' => 'Groceries', 'position' => 0]);

    $this->actingAs($user)->get('/lists')->assertRedirect(route('lists.show', $list));
});

test('a user can create a new list', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/lists', ['name' => 'Groceries']);

    $list = TodoList::first();
    $response->assertRedirect(route('lists.show', $list));
    expect($list->name)->toBe('Groceries');
    expect($list->user_id)->toBe($user->id);
});

test('new lists are appended after existing ones by position', function () {
    $user = User::factory()->create();
    $user->todoLists()->create(['name' => 'First', 'position' => 0]);

    $this->actingAs($user)->post('/lists', ['name' => 'Second']);

    expect(TodoList::where('name', 'Second')->first()->position)->toBe(1);
});

test('a user can rename their own list', function () {
    $user = User::factory()->create();
    $list = $user->todoLists()->create(['name' => 'Old name', 'position' => 0]);

    $response = $this->actingAs($user)->patch("/lists/{$list->id}", ['name' => 'New name']);

    $response->assertRedirect(route('lists.show', $list));
    expect($list->fresh()->name)->toBe('New name');
});

test('a user can delete their own list', function () {
    $user = User::factory()->create();
    $list = $user->todoLists()->create(['name' => 'Gone soon', 'position' => 0]);

    $response = $this->actingAs($user)->delete("/lists/{$list->id}");

    $response->assertRedirect(route('lists.index'));
    $this->assertDatabaseMissing('todo_lists', ['id' => $list->id]);
});

test('deleting a list deletes its tasks too', function () {
    $user = User::factory()->create();
    $list = $user->todoLists()->create(['name' => 'Gone soon', 'position' => 0]);
    $task = $list->tasks()->create(['title' => 'A task', 'position' => 0]);

    $this->actingAs($user)->delete("/lists/{$list->id}");

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('a user cannot view, rename, or delete someone elses list', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $list = $owner->todoLists()->create(['name' => 'Private', 'position' => 0]);

    $this->actingAs($intruder)->get("/lists/{$list->id}")->assertNotFound();
    $this->actingAs($intruder)->patch("/lists/{$list->id}", ['name' => 'Hijacked'])->assertNotFound();
    $this->actingAs($intruder)->delete("/lists/{$list->id}")->assertNotFound();

    expect($list->fresh()->name)->toBe('Private');
});

test('the sidebar only shows the current users own lists', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $mine = $user->todoLists()->create(['name' => 'Mine', 'position' => 0]);
    $other->todoLists()->create(['name' => 'Not mine', 'position' => 0]);

    $response = $this->actingAs($user)->get(route('lists.show', $mine));

    $response->assertOk();
    $response->assertSee('Mine');
    $response->assertDontSee('Not mine');
});
