<!DOCTYPE html>
<html>
<body>

<h2>Mentor Assigned</h2>

<p>Hello {{ $user->first_name }},</p>

<p>
    A mentor has been assigned to your team.
</p>

<p>
    Team:
    <strong>{{ $team->name }}</strong>
</p>

<p>
    Mentor:
    <strong>
        {{ $mentor->user->first_name }}
        {{ $mentor->user->last_name }}
    </strong>
</p>

<p>
    You can now contact your mentor through the platform.
</p>

</body>
</html>
