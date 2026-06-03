<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>

<h2>
    Welcome {{ $user->first_name }} {{ $user->last_name }}!
</h2>

<p>
    Your account has been successfully created in the NTI Project Management System.
</p>

<p>
    You can now log in and start using the platform.
</p>

<p>
    Role: {{ $user->roles->pluck('name')->join(', ') }}
</p>

<br>

<p>
    NTI Project System
</p>

</body>
</html>
