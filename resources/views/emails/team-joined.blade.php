<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Team Joined</title>
</head>
<body>
<h2>Hello {{ $user->name }},</h2>

<p>
    You have successfully joined the team
    <strong>{{ $team->name }}</strong>.
</p>

<p>
    You can now participate in team activities and project applications.
</p>

<br>

<p>
    Best regards,<br>
    NTI Team
</p>
</body>
</html>
