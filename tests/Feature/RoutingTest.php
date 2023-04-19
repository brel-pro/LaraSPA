<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoutingTest extends TestCase
{
    /** @var User */
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function api_route_test_no_auth()
    {
        $response = $this->getJson(route('testapiroutenoauth'))
            ->assertSuccessful();
    }

    /** @test */
    public function api_route_test_with_auth()
    {
        $response = $this->postJson(route('login'), [
            'email' => $this->user->email,
            'password' => 'password',
            'device_name' => 'spa',
        ]);

        $token = $response->json()['token'];

        $response = $this->withToken($token)
            ->getJson(route('testapiroutewithauth'))
            ->assertSuccessful();
    }

    /** @test */
    public function web_route_test_no_auth()
    {
        $response = $this->get(route('testwebroutenoauth'))
            ->assertSuccessful();
    }

    /** @test */
    public function web_route_test_with_auth()
    {
        $response = $this->postJson(route('login'), [
            'email' => $this->user->email,
            'password' => 'password',
            'device_name' => 'spa',
        ]);

        $token = $response->json()['token'];

        $response = $this->withToken($token)
            ->get(route('testwebroutewithauth'))
            ->assertSuccessful();
    }
}
