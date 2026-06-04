<!DOCTYPE html>
<html>
<body>

<h2>Successful Login</h2>

<p>Hello {{ $user->first_name }}!</p>

<p>
    A successful login was detected on your account.
</p>

<p>
    Login time: {{ now() }}
</p>

<p>
    If this wasn't you, please change your password immediately.
</p>

</body>
</html>
