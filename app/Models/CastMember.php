<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use SoftDeletes, Traits\Uuid;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'type'];
    protected $dates = ['deleted_at'];
}
