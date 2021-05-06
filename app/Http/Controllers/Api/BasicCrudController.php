<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class BasicCrudController extends Controller
{
    protected abstract function model();
    protected abstract function rulesStore();

    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $validatedDate = $this->validate($request, $this->rulesStore());
        $obj = $this->model()::create($validatedDate);
        $obj->refresh();
        return $obj;
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();
    }
}
