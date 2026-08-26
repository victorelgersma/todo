<?php

use App\Models\User;
use App\Notifications\LoginLinkNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

test('login screen can be rendered', function () {
    $this->get('/login')->assertStatus(200);
});

test('requesting a login link sends a notification and creates a new user if needed', function () {
    Notification::fake();

    $response = $this->post('/login', ['email' => 'new@example.com']);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status', 'login-link-sent');

    $user = User::where('email', 'new@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBeNull();

    Notification::assertSentTo($user, LoginLinkNotification::class);
});

test('visiting a valid signed login link authenticates a user with a name and sends them to their lists', function () {
    $user = User::factory()->create();

    $url = URL::temporarySignedRoute('login.verify', now()->addMinutes(15), ['user' => $user->id]);
    $response = $this->get($url);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('lists.index'));
});

test('a user with no name yet is sent to onboarding after clicking their login link', function () {
    $user = User::factory()->create(['name' => null]);

    $url = URL::temporarySignedRoute('login.verify', now()->addMinutes(15), ['user' => $user->id]);
    $response = $this->get($url);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('onboarding.name'));
});

test('an unsigned login link is rejected', function () {
    $user = User::factory()->create();

    $this->get("/login/{$user->id}")->assertForbidden();
    $this->assertGuest();
});

test('completing onboarding sets the name and redirects to lists', function () {
    $user = User::factory()->create(['name' => null]);

    $response = $this->actingAs($user)->post('/onboarding/name', ['name' => 'Victor']);

    $response->assertRedirect(route('lists.index'));
    expect($user->fresh()->name)->toBe('Victor');
});

test('a user with no name is redirected to onboarding from any other page', function () {
    $user = User::factory()->create(['name' => null]);

    $this->actingAs($user)->get('/lists')->assertRedirect(route('onboarding.name'));
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
