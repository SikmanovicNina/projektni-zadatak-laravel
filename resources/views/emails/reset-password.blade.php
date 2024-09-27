<!DOCTYPE html>
<html>
<head>
    <title>Reset your password</title>
</head>
<body>
<h1>Reset your password</h1>
<p>Click the link below to reset your password:</p>
<a href="{{ url('password/reset', $token) }}">Reset password</a>
</body>
</html>
