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
        $response = $this->get(route('chirps.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function users_can_see_chirps()
    {
        $this->signIn();

        $response = $this->get(route('chirps.index'));

        $response->assertOk();
    }

    /** @test */
    public function guests_cant_create_chirps()
    {
        $response = $this->post(route('chirps.store'), ['message' => 'Testing']);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function a_user_can_only_update_his_chirps()
    {
        $john = User::factory()->create();
        $jane = User::factory()->create();
        $chirp = Chirp::factory()->for($john)->create();

        $this->signIn($jane);

        $response = $this->patch(route('chirps.update', $chirp), ['message' => 'Testing']);

        $response->assertForbidden();
    }

    /** @test */
    public function a_user_can_only_delete_his_chirps()
    {
        $john = User::factory()->create();
        $jane = User::factory()->create();
        $chirp = Chirp::factory()->for($john)->create();

        $this->signIn($jane);

        $response = $this->delete(route('chirps.destroy', $chirp));

        $response->assertForbidden();
    }

    /** @test */
    public function users_are_notified_of_new_chirps()
    {
        Notification::fake();

        $john = User::factory()->create();
        $jane = User::factory()->create();

        $this->signIn($john);

        $this->post(route('chirps.store'), ['message' => 'Testing']);

        Notification::assertSentTo($jane, NewChirp::class);
    }
}
