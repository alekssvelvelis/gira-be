<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'organization_name' => 'required|unique:organizations,organization_name',
            'organization_identifier' => 'required|min:5|max:5|unique',
            'organization_description' => 'required|string',
            'organization_picture' => 'nullable'
        ]);
    }
}
