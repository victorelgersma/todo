<?php

use App\Models\User;

test('the overview orders lists by the date of their most recently added task', function () {
    $user = User::factory()->create();

    $stale = $user->todoLists()->create(['name' => 'Stale project', 'position' => 0]);
    $busy = $user->todoLists()->create(['name' => 'Busy project', 'position' => 1]);
    $empty = $user->todoLists()->create(['name' => 'Empty project', 'position' => 2]);

    $this->travelTo(now()->subDays(10));
    $stale->tasks()->create(['title' => 'Old task', 'position' => 0]);
    $busy->tasks()->create(['title' => 'Early task', 'position' => 0]);
    $empty->update(['created_at' => now()->subDays(5)]);
    $this->travelBack();

    $busy->tasks()->create(['title' => 'Fresh task', 'position' => 1]);

    $this->actingAs($user)->get(route('lists.overview'))
        ->assertOk()
        ->assertSeeInOrder(['Busy project', 'Empty project', 'Stale project']);
});

test('the overview shows task counts and a preview of open tasks', function () {
    $user = User::factory()->create();
    $list = $user->todoLists()->create(['name' => 'Groceries', 'position' => 0]);
    $list->tasks()->create(['title' => 'Buy milk', 'position' => 0]);
    $list->tasks()->create(['title' => 'Buy eggs', 'position' => 1, 'completed_at' => now()]);

    $this->actingAs($user)->get(route('lists.overview'))
        ->assertOk()
        ->assertSee('1 open')
        ->assertSee('1 done')
        ->assertSee('Buy milk')
        ->assertDontSee('Buy eggs');
});

test('the overview only shows the current users own lists', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $user->todoLists()->create(['name' => 'Mine', 'position' => 0]);
    $other->todoLists()->create(['name' => 'Not mine', 'position' => 0]);

    $this->actingAs($user)->get(route('lists.overview'))
        ->assertOk()
        ->assertSee('Mine')
        ->assertDontSee('Not mine');
});

test('guests cannot see the overview', function () {
    $this->get(route('lists.overview'))->assertRedirect(route('login'));
});
