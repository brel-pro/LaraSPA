<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /** @var User */
    protected $user;
    protected $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticate()
    {
        $response = $this->postJson(route('login'), [
            'email' => $this->user->email,
            'password' => 'password',
            'device_name' => 'spa',
        ]);

        $token = $response->json()['token'];
        $this->token = $token;

        $response
            ->assertSuccessful()
            ->assertJson(['token' => $token])
            ->assertHeader('Authorization');

        $this->withToken($token)
            ->postJson(route('me'))
            ->assertSuccessful();

        $this->assertDatabaseHas(PersonalAccessToken::TABLE_NAME, [
            PersonalAccessToken::COLUMN_NAME => 'spa',
            PersonalAccessToken::COLUMN_TOKENABLE_ID => $this->user->id,
            PersonalAccessToken::COLUMN_TOKENABLE_TYPE => User::class,
        ]);

    }

    /** @test */
    public function fetch_the_current_user()
    {
        $this->actingAs($this->user)
            ->postJson(route('me'))
            ->assertSuccessful()
            ->assertJsonPath('data.email', $this->user->email);
    }

    /**
     * @test
     */
    public function log_out()
    {

        Config::set('auth.defaults.guard', 'api');

        $response = $this->postJson(route('login'), [
            'email' => $this->user->email,
            'password' => 'password',
            'device_name' => 'spa',
        ]);

        $token = $response->json()['token'];

        $this->assertDatabaseHas(PersonalAccessToken::TABLE_NAME, [
            PersonalAccessToken::COLUMN_NAME => 'spa',
            PersonalAccessToken::COLUMN_TOKENABLE_ID => $this->user->id,
            PersonalAccessToken::COLUMN_TOKENABLE_TYPE => User::class,
        ]);

        $userID = $this->user->id;
        $user = $this->user;

        $response = $this
            ->withToken($token)
            ->postJson(route('logout'));

        // Not working. Need another method of testing this.
        ///https://laracasts.com/discuss/channels/testing/tdd-with-sanctum-issue-with-user-logout-case

        $response = $this->withToken($token)
            ->postJson(route('me'));

        $response->assertStatus(401);
    }
}
