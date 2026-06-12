<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrganizationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'organization_name' => 'required|unique:organizations,organization_name',
            'organization_identifier' => 'required|min:5|max:5|unique:organizations,organization_identifier',
            'organization_description' => 'required|string',
            'organization_picture' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $file = $validated['organization_picture'];
        $extension = $file->getClientOriginalExtension();
        $filename = 'org_' . $validated['organization_identifier'] . '.' . $extension;
        $path = $file->storeAs('organizations', $filename, 'public');

        $organization = Organization::create([
            'organization_name' => $validated['organization_name'],
            'organization_identifier' => $validated['organization_identifier'],
            'organization_description' => $validated['organization_description'],
            'owner_id' => $request->user()->id,
            'organization_picture' => $path
        ]);

        $organization->users()->attach($request->user()->id, ['role' => 'owner']);
        return response()->json([
            'message' => 'Organization created succesfully', 
            'organization' => $organization
        ], 201);
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'organization_name' => 'required|unique:organizations,organization_name,' . $organization->id,
            'organization_identifier' => 'required|min:5|max:5|unique:organizations,organization_identifier,' . $organization->id,
            'organization_description' => 'required|string',
            'organization_picture' => $request->hasFile('organization_picture') 
            ? 'image|mimes:jpeg,png,jpg,webp|max:2048' 
            : 'nullable|string'
        ]);

        $organization->update([
            'organization_name' => $validated['organization_name'],
            'organization_identifier' => $validated['organization_identifier'],
            'organization_description' => $validated['organization_description'],
        ]);

        if ($request->hasFile('organization_picture')) {
            if ($organization->organization_picture && \Storage::disk('public')->exists($organization->organization_picture)) {
                \Storage::disk('public')->delete($organization->organization_picture);
            }      
            $file = $validated['organization_picture'];
            $extension = $file->extension();
            $filename = 'org_' . $validated['organization_identifier'] . '.' . $extension;
            $path = $file->storeAs('organizations', $filename, 'public');
            $organization->update(['organization_picture' => $path]);
        }

        return response()->json([
            'message' => 'Organization updated successfully',
            'organization' => $organization
        ], 200);
    }

    public function index(Request $request)
    {
        return $request->user()->organizations()->get();
    }

    public function show(Request $request, Organization $organization)
    {   
        $userOrganization = $request->user()
        ->organizations()
        ->where('organization_id', $organization->id)
        ->with('owner:id,name,email')
        ->firstOrFail();

        return response()->json($userOrganization, 200);
    }

    public function users(Organization $organization)
    {
        $users = $organization->users()->select('users.id', 'users.nickname', 'users.email')->get();
        // $users = $organization->users;
        return response()->json($users, 200);
    }
}
