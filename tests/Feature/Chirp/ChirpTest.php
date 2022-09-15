<?php

namespace Tests\Feature\Chirp;

use Tests\TestCase;
use App\Models\User;
use App\Models\Chirp;
use App\Notifications\NewChirp;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChirpTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cant_see_chirps()
    {
        $this->get(route('chirps.index'))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function users_can_see_chirps()
    {
        $this->signIn()
            ->get(route('chirps.index'))
            ->assertOk();
    }

    /** @test */
    public function guests_cant_create_chirps()
    {
        $this->post(route('chirps.store'), ['message' => 'Testing'])
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function users_can_only_update_their_chirps()
    {
        $john = User::factory()->create();
        $jane = User::factory()->create();
        $chirp = Chirp::factory()->for($john)->create();

        $response = $this->signIn($jane)
            ->patch(route('chirps.update', $chirp), ['message' => 'Testing'])
            ->assertForbidden();
    }

    /** @test */
    public function users_can_only_delete_their_chirps()
    {
        $john = User::factory()->create();
        $jane = User::factory()->create();
        $chirp = Chirp::factory()->for($john)->create();

        $this->signIn($jane)
            ->delete(route('chirps.destroy', $chirp))
            ->assertForbidden();
    }

    /** @test */
    public function users_are_notified_when_another_one_creates_a_chirp()
    {
        Notification::fake();

        $john = User::factory()->create();
        $jane = User::factory()->create();

        $this->signIn($john)
            ->post(route('chirps.store'), ['message' => 'Testing']);

        Notification::assertSentTo($jane, NewChirp::class);
    }
}
