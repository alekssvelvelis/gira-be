<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Organization;

class ProjectController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => 'required',
            'project_description' => 'required',
            'organization_id' => 'required|exists:organizations,id',
        ]);

        $project = Project::create([
            'project_name' => $validated['project_name'],
            'project_description' => $validated['project_description'],
            'organization_id' => $validated['organization_id'],
        ]);

        return response()->json([
            'message' => 'Project created succesfully', 
            'project' => $project
        ], 201);
    }

    public function update(Request $request, Organization $organization, Project $project)
    {
        $validated = $request->validate([
            'project_name' => 'required',
            'project_description' => 'required',
        ]);

        abort_if($project->organization_id !== $organization->id, 404);

        $project->update([
            'project_name' => $validated['project_name'],
            'project_description' => $validated['project_description'],
        ]);

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project
        ], 200);
    }

    public function index(Request $request, Organization $organization)
    {   
        $isMember = $request->user()
        ->organizations()
        ->where('organization_id', $organization->id)
        ->exists();
        abort_if(!$isMember, 403, 'Unauthorized');

        $projects = $organization->projects()->get();

        return response()->json($projects, 200);
    }

    public function show(Request $request, Organization $organization, Project $project)
    {
        $isMember = $request->user()
            ->organizations()
            ->where('organization_id', $organization->id)
            ->exists();
        abort_if(!$isMember, 403, 'Unauthorized');

        abort_if($project->organization_id !== $organization->id, 404);

        return response()->json($project, 200);
    }
}
