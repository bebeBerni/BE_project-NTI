<!DOCTYPE html>
<html>
<body>

<h2>Application Submitted Successfully</h2>

<p>
    Hello {{ $user->first_name }},
</p>

<p>
    Your application has been successfully submitted.
</p>

<p>
    Project:
    <strong>{{ $application->project->title }}</strong>
</p>

<p>
    Team:
    <strong>{{ $application->team->name }}</strong>
</p>

<p>
    Category:
    <strong>{{ $application->category->name }}</strong>
</p>

<p>
    Current status:
    <strong>{{ $application->status }}</strong>
</p>

<p>
    You will be notified when the application status changes.
</p>

</body>
</html>
