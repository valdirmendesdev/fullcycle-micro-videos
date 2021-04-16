<?php

namespace Tests\Unit\Models;

use App\Models\Genre;
use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\SoftDeletes;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase
{
    private $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new Genre();
    }

    public function testFillableAttribute()
    {
        $fillable = ['name', 'is_active'];

        $this->assertEquals(
            $fillable,
            $this->sut->getFillable()
        );
    }

    public function testIfUseTraits()
    {
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $sutTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits, $sutTraits);
    }

    public function testIncrementing()
    {
        $this->assertFalse($this->sut->incrementing);
    }

    public function testDatesAttribute()
    {
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->sut->getDates());
        }
        $this->assertCount(count($dates), $this->sut->getDates());
    }

    public function testCasts()
    {
        $casts = ['is_active' => 'boolean'];
        $this->assertEquals($casts, $this->sut->getCasts());
    }
}
