<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

use App\Models\User;
use App\Models\Project;
use App\Models\Tasks;

#[Fillable(['organization_name', 'organization_identifier', 'owner_id', 'organization_description', 'organization_picture'])]
class Organization extends Model
{
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user')->withPivot('role')->withTimestamps();
    }

    public function members(): belongsToMany 
    {
        return $this->belongsToMany(User::class)->wherePivot('status', 'accepted');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'organization_id');
    }

    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(Tasks::class, Project::class);
    }
}
