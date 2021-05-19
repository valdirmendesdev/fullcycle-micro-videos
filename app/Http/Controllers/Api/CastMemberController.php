<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Validation\Rule;

class CastMemberController extends BasicCrudController
{
    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'type' => ['required', Rule::in([CastMember::TYPE_DIRECTOR, CastMember::TYPE_ACTOR])],
        ];
    }

    protected function model()
    {
        return CastMember::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }
}
