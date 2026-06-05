<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>

<h2>Project Deadline Reminder</h2>

<p>Hello {{ $user->name }},</p>

<p>
    This is a reminder that the project
    <strong>{{ $project->title }}</strong>
    deadline is approaching.
</p>

<p>
    @if($daysRemaining === 1)
        The deadline is tomorrow.
    @else
        The deadline is in {{ $daysRemaining }} days.
    @endif
</p>

<p>
    Deadline:
    <strong>{{ $project->deadline }}</strong>
</p>

<p>
    Please make sure everything is submitted before the deadline.
</p>

<p>
    NTI Project System
</p>

</body>
</html>
