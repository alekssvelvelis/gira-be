<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Organization;

class UserController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function index(Request $request)
    {
        // return response()->json(User::findOrFail($id));
    }

    public function update(Request $request)
    {
        
    }

    public function userOrganization(Request $request)
    {
        $organizations = $request->user()->organizations;
        return response()->json($organizations);
    }
}
