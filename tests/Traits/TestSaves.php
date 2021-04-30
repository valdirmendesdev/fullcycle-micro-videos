<?php

declare(strict_types=1);

namespace Tests\Traits;

use Exception;
use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves
{

  protected abstract function model();
  protected abstract function routeStore();
  protected abstract function routeUpdate();

  protected function assertStore(array $sendData, array $testData, array $testJsonData = null): TestResponse
  {
    /** @var TestResponse $response */
    $response = $this->json('POST', $this->routeStore(), $sendData);
    if ($response->status() !== 201) {
      throw new Exception("Response status must be 201, give {$response->status()}: \n{$response->content()}");
    }
    $this->assertInDatabase($response, $testData);
    $this->assertJsonResponseContent($response, $testData, $testJsonData);
    return $response;
  }

  protected function assertUpdate(array $sendData, array $testData, array $testJsonData = null): TestResponse
  {
    /** @var TestResponse $response */
    $response = $this->json('PUT', $this->routeUpdate(), $sendData);
    if ($response->status() !== 200) {
      throw new Exception("Response status must be 200, give {$response->status()}: \n{$response->content()}");
    }
    $this->assertInDatabase($response, $testData);
    $this->assertJsonResponseContent($response, $testData, $testJsonData);
    return $response;
  }

  private function assertInDatabase(TestResponse $response, array $testDatabase)
  {
    $model = $this->model();
    $table = (new $model)->getTable();
    $this->assertDatabaseHas($table, $testDatabase + ['id' => $response->json('id')]);
  }

  private function assertJsonResponseContent(TestResponse $response, array $testDatabase, array $testJsonData = null)
  {
    $testResponse = $testJsonData ?? $testDatabase;
    $response->assertJsonFragment($testResponse + ['id' => $response->json('id')]);
  }
}
