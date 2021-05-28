<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes, Traits\Uuid;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'description', 'is_active'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
}
