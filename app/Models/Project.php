<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Tasks;
use App\Models\Organization;

#[Fillable(['project_name', 'project_description', 'organization_id'])]

class Project extends Model
{
    public function owner_organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Tasks::Class, 'project_id');
    }
}
