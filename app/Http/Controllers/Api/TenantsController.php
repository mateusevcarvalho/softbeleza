<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantsController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Tenant::getResults($request->all()));
    }
}
