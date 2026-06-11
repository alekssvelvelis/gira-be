<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Tasks;

#[Fillable(['project_name', 'project_description', 'project_status'])]

class Project extends Model
{
    // public function tasks(): hasMany
    // {
    //     return $this->hasMany(Tasks:Class, 'task_id');
    // }
}
