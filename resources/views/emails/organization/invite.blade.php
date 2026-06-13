<x-mail::message>
# Introduction

{{ $invitation->inviter->email }} has invited you to join {{ $invitation->organization->organization_name }}.

<x-mail::button :url="config('app.frontend_url') . '/invitations/' . $invitation->token">
Accept invitation
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
