<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProvinceResource;
use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{

    public function index()
    {
        try {
            $data = Province::orderBy('title')->get();
            return response(ProvinceResource::collection($data), 200);
        }catch(\Exception $exception){
            return $exception;
        }
    }

}
