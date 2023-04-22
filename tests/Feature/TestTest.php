<?php

use App\Http\Controllers\Controller;
use App\Models\Test;

test('create test', function () {
    $this->actingAs($this->user)
        ->postJson(route('tests.store'), [
            'name' => 'Lorem',
        ])
        ->assertSuccessful()
        ->assertJson(['type' => Controller::RESPONSE_TYPE_SUCCESS]);

    $this->assertDatabaseHas('tests', [
        'name' => 'Lorem',
    ]);
});

test('update test', function () {
    $test = Test::factory()->create();

    $this->actingAs($this->user)
        ->putJson(route('tests.update', $test->id), [
            'name' => 'Updated test',
        ])
        ->assertSuccessful()
        ->assertJson(['type' => Controller::RESPONSE_TYPE_SUCCESS]);

    $this->assertDatabaseHas('tests', [
        'id' => $test->id,
        'name' => 'Updated test',
    ]);
});

test('show test', function () {
    $test = Test::factory()->create();

    $this->actingAs($this->user)
        ->getJson(route('tests.show', $test->id))
        ->assertSuccessful()
        ->assertJson([
            'data' => [
                'name' => $test->name,
            ],
        ]);
});

test('list test', function () {
    $tests = Test::factory()->count(2)->create()->map(function ($test) {
        return $test->only(['id', 'name']);
    });

    $this->actingAs($this->user)
        ->getJson(route('tests.index'))
        ->assertSuccessful()
        ->assertJson([
            'data' => $tests->toArray(),
        ])
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name'],
            ],
            'links',
            'meta',
        ]);
});

test('delete test', function () {
    $test = Test::factory()->create([
        'name' => 'Test for delete',
    ]);

    $this->actingAs($this->user)
        ->deleteJson(route('tests.update', $test->id))
        ->assertSuccessful()
        ->assertJson(['type' => Controller::RESPONSE_TYPE_SUCCESS]);

    $this->assertDatabaseMissing('tests', [
        'id' => $test->id,
        'name' => 'Test for delete',
    ]);
});
