<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;

use App\Models\User;
use App\Models\Project;

#[Fillable(['task_status', 'task_description', 'task_type', 'assignee_id', 'due_date', 'priority', 'project_id'])]

class Tasks extends Model
{
    public function assignee(): belongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
    
    public function project(): belongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
