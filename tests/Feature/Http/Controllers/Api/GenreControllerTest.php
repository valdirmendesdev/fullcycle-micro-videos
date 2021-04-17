<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function createGenre(array $attributes = [])
    {
        return factory(Genre::class)->create($attributes);
    }

    public function doHttpRequest(string $controllerAction, string $httpMethod = 'GET',  array $data = [])
    {
        $route = 'genres.' . $controllerAction;
        return $this->json(
            $httpMethod,
            route(
                $route,
                $data
            )
        );
    }

    public function testIndex()
    {
        $genre = $this->createGenre();
        $response = $this->doHttpRequest('index');
        $response
            ->assertOk()
            ->assertJson([$genre->toArray()]);
    }

    public function testShow()
    {
        $genre = $this->createGenre();
        $response = $this->doHttpRequest('show', 'GET', ['genre' => $genre->id]);
        $response
            ->assertOk()
            ->assertJson($genre->toArray());
    }

    public function testStore()
    {
        $response = $this->doHttpRequest('store', 'POST', ['name' => 'test']);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertCreated()
            ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));

        $response = $this->doHttpRequest('store', 'POST', [
            'name' => 'test',
            'is_active' => false
        ]);
        $response->assertJsonFragment([
            'is_active' => false
        ]);
    }

    public function testUpdate()
    {
        $genre = $this->createGenre([
            'is_active' => false
        ]);
        $response = $this->doHttpRequest('update', 'PUT', [
            'genre' => $genre->id,
            'name' => 'test',
            'is_active' => true
        ]);

        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertOk()
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'name' => 'test',
                'is_active' => true
            ]);
    }

    public function testDelete()
    {
        $genre = $this->createGenre();
        $response = $this->doHttpRequest('destroy', 'DELETE', ['genre' => $genre->id]);
        $response->assertNoContent();
        $this->assertNull(Genre::find($genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($genre->id));
    }

    public function testInvalidData()
    {
        $response = $this->doHttpRequest('store', 'POST', []);
        $this->assertRequiredFields($response);

        $response = $this->doHttpRequest('store', 'POST', [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);
    }

    protected function assertRequiredFields(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                Lang::get('validation.required', ['attribute' => 'name'])
            ]);
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonFragment([
                Lang::get('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ]);
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['is_active'])
            ->assertJsonFragment([
                Lang::get('validation.boolean', ['attribute' => 'is active'])
            ]);
    }
}
