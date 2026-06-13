<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Models\Organization;
use App\Models\OrganizationInvitation;

use App\Mail\OrganizationInviteMail;

class InvitationController extends Controller
{
    public function store(Request $request, Organization $organization)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $invitedUser = User::where('email', $request->email)->first();

        if (!$invitedUser) {
            return response()->json([
                'message' => 'No account found with that email address.',
            ], 422);
        }

        $alreadyMember = $organization->users()->where('user_id', $invitedUser->id)->exists();
        if ($alreadyMember) {
            return response()->json([
                'message' => 'This user is already a member of the organization.',
            ], 422);
        }

        // Prevent duplicate pending invites
        $pendingExists = OrganizationInvitation::where('organization_id', $organization->id)
            ->where('email', $request->email)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->exists();

        if ($pendingExists) {
            return response()->json([
                'message' => 'A pending invitation already exists for this email.',
            ], 422);
        }

        $invitation = OrganizationInvitation::create([
            'organization_id' => $organization->id,
            'invited_by' => auth()->id(),
            'invited_user_id' => $invitedUser->id,
            'email' => $request->email,
            'token' => Str::random(64),
            'expires_at' => now()->addDays(7),
        ]);

        try {
            Mail::to($request->email)->send(new OrganizationInviteMail($invitation));
        } catch (\Exception $e) {
            \Log::error('Invitation mail failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Invitation created but email could not be sent.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'message' => 'Invitation sent successfully.',
        ], 201);
    }

    public function accept(string $token)
    {
        $invitation = OrganizationInvitation::with('organization')
            ->where('token', $token)
            ->firstOrFail();

        if ($invitation->status === 'accepted' && $invitation->invited_user_id === auth()->id()) {
            return response()->json([
                'message' => "You've successfully joined {$invitation->organization->organization_name}!",
            ], 200);
        }

        if ($invitation->isExpired()) {
            return response()->json([
                'message' => 'This invitation has expired or has already been used.',
            ], 410);
        }

        $user = User::where('email', $invitation->email)->firstOrFail();

        $invitation->organization->users()->syncWithoutDetaching([$user->id]);
        $invitation->update(['status' => 'accepted']);

        return response()->json([
            'message' => "You've successfully joined {$invitation->organization->name}!",
        ], 200);
    }
}
