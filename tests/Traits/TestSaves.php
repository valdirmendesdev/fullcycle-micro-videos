<?php

declare(strict_types=1);

namespace Tests\Traits;

use Exception;
use Illuminate\Foundation\Testing\TestResponse;

trait TestSaves
{
  protected function assertStore(array $sendData, array $testData, array $testJsonData = null)
  {
    /** @var TestResponse $response */
    $response = $this->json('POST', $this->routeStore(), $sendData);
    if ($response->status() !== 201) {
      throw new Exception("Response status must be 201, give {$response->status()}: \n{$response->content()}");
    }
    $model = $this->model();
    $table = (new $model)->getTable();
    $this->assertDatabaseHas($table, $testData + ['id' => $response->json('id')]);
    $testResponse = $testJsonData ?? $testData;
    $response->assertJsonFragment($testResponse + ['id' => $response->json('id')]);
    return $response;
  }
}
