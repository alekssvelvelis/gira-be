<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Organization;
use App\Models\User;

#[Fillable(['organization_id', 'invited_by', 'invited_user_id', 'email', 'token', 'status', 'expires_at'])]

class OrganizationInvitation extends Model
{

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function invitedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_user_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast() || $this->status !== 'pending';
    }
}
