<!DOCTYPE html>
<html>
<body>

<h2>Application Status Updated</h2>

<p>
    Hello {{ $user->first_name }},
</p>

<p>
    The status of your application has changed.
</p>

<p>
    Project:
    <strong>{{ $application->project->title }}</strong>
</p>

<p>
    Previous status:
    <strong>{{ $oldStatus }}</strong>
</p>

<p>
    New status:
    <strong>{{ $newStatus }}</strong>
</p>

</body>
</html>
