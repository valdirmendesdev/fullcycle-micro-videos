<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    private $category;
    public $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory(Category::class)->create();
        $this->sendData = [
            'name' => 'test',
            'description' => null,
        ];
    }

    public function testIndex()
    {
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->category->toArray()]);
    }

    public function testShow()
    {
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->category->toArray());
    }

    public function testInvalidationRequired()
    {
        $data = [
            'name' => '',
            'genres_id' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationBoolean()
    {
        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testInvalidationMax()
    {
        $data = [
            'name' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationRelationships()
    {
        $data = [
            'genres_id' => 's'
        ];

        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'genres_id' => [100]
        ];

        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    protected function assertInvalidationRequired(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['name'], 'required');
        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['name'], 'max.string', ['max' => 255]);
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {
        $this->assertInvalidationFields($response, ['is_active'], 'boolean');
        $response->assertJsonValidationErrors(['is_active']);
    }

    public function testSave()
    {
        $genre = factory(Genre::class)->create();
        $data = [
            [
                'send_data' => $this->sendData + [
                    'genres_id' => [$genre->id]
                ],
                'test_data' => $this->sendData + [
                    'is_active' => true
                ],
            ],
            [
                'send_data' => $this->sendData + [
                    'name' => 'test',
                    'description' => 'description',
                    'is_active' => false,
                    'genres_id' => [$genre->id]
                ],
                'test_data' => $this->sendData + [
                    'name' => 'test',
                    'description' => 'description',
                    'is_active' => false,
                ],
            ],
            [
                'send_data' => $this->sendData + [
                    'description' => null,
                    'genres_id' => [$genre->id]
                ],
                'test_data' => $this->sendData + [
                    'description' => null,
                ],
            ]
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'created_at',
                'updated_at'
            ]);
            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'created_at',
                'updated_at'
            ]);
        }
    }

    public function testDelete()
    {
        $response = $this->json(
            'DELETE',
            route('categories.destroy', [
                'category' => $this->category->id
            ])
        );
        $response->assertNoContent();
        $this->assertNull(Category::find($this->category->id));
        $this->assertNotNull(Category::withTrashed()->find($this->category->id));
    }

    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function model()
    {
        return Category::class;
    }
}
