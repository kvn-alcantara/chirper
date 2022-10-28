<?php

use App\Models\Chirp;
use App\Models\User;
use App\Notifications\NewChirp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('guests cant see chirps', function () {
    get(route('chirps.index'))
        ->assertRedirect(route('login'));
});

test('users can see chirps', function () {
    signIn()
        ->get(route('chirps.index'))
        ->assertOk();
});

test('guests cannot create chirps', function () {
    post(route('chirps.store'), ['message' => 'Pest is awesome'])
        ->assertRedirect(route('login'));
});

test('users can only update their chirps', function () {
    $john = User::factory()->create();
    $jane = User::factory()->create();
    $chirp = Chirp::factory()->for($john)->create();

    signIn($jane)
        ->patch(route('chirps.update', $chirp), ['message' => 'Pest is awesome'])
        ->assertForbidden();
});

test('users can only delete their chirps', function () {
    $john = User::factory()->create();
    $jane = User::factory()->create();
    $chirp = Chirp::factory()->for($john)->create();

    signIn($jane)
        ->delete(route('chirps.destroy', $chirp))
        ->assertForbidden();
});

test('users are notified when another one creates a chirp', function () {
    Notification::fake();

    $john = User::factory()->create();
    $jane = User::factory()->create();

    signIn($john)
        ->post(route('chirps.store'), ['message' => 'Pest is awesome']);

    Notification::assertSentTo($jane, NewChirp::class);
});
