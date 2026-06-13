<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Tasks;
use App\Models\Organization;
use App\Models\Project;

class TasksController extends Controller
{
    public function store(Request $request, $organizationId, $projectId)
    {
        $validated = $request->validate([
            'task_description' => 'required|max:350',
            'task_type' => 'required|string',
            'priority' => 'required|min:1|max:5',
            'assignee_id' => 'required|exists:users,id',
            'due_date' => 'required|date|after:today',
        ]);

        $task = Tasks::create([
            'task_description' => $validated['task_description'],
            'task_type' => $validated['task_type'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'project_id' => $projectId,
            'assignee_id' => $validated['assignee_id'],
        ]);

        return response()->json([
            'message' => 'Task created succesfully', 
            'task' => $task
        ], 201);
    }

    public function update(Request $request, Organization $organization, Project $project, Tasks $task)
    {
        $validated = $request->validate([
            'task_description' => 'required|max:350',
            'task_type' => 'required|string',
            'priority' => 'required|min:1|max:5',
            'assignee_id' => 'required|exists:users,id',
            'due_date' => 'required|date|after:today',
            'task_status' => 'required|string',
        ]);

        $task->update([
            'task_description' => $validated['task_description'],
            'task_type' => $validated['task_type'],
            'due_date' => $validated['due_date'],
            'priority' => $validated['priority'],
            'assignee_id' => $validated['assignee_id'],
            'task_status' => $validated['task_status'],
        ]);

        return response()->json([
            'message' => 'Project updated successfully',
            'project' => $project
        ], 200);
    }

    public function index(Request $request, Organization $organization, Project $project)
    {
        abort_if(
            !$request->user()->organizations()->where('organization_id', $organization->id)->exists(),
            403,
            'Unauthorized'
        );

        abort_if($project->organization_id !== $organization->id, 404);

        $tasks = $project->tasks()->with(['assignee:id,nickname,email,profile_picture'])->get();

        return response()->json($tasks, 200);
    }

    public function show(Request $request, Organization $organization, Project $project, Tasks $task)
    {
        $isMember = $request->user()
            ->organizations()
            ->where('organization_id', $organization->id)
            ->exists();
        abort_if(!$isMember, 403, 'Unauthorized');

        abort_if($project->organization_id !== $organization->id, 404, 'Project does not belong to organization');
        // return response()->json(['message' => 'returned']);
        $task->load([
            'assignee:id,nickname,email,profile_picture',
            'project:id,project_name',
        ]);

        return response()->json([
            'task' => $task, 
            'is_owner' => $organization->owner_id === $request->user()->id || $request->user()->id === $task->assignee_id
        ], 200);
    }
}
