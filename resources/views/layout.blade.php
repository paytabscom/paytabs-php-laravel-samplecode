<!DOCTYPE html>
<html>
<head>
    <title>Laravel 8 CRUD Application - laravelcode.com</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <table border=1 cellspacing="5" cellpadding="5" style="border: 1px solid blue; padding: 15px; margin: 15px;">
        <tr> <td><a href="{{ route('index') }}">Home</a></td>
            <td><a href="{{ route('hosted_payment') }}">Hosted payment</a></td>
            <td><a href="{{ route('managed_form') }}">Managed Form</a></td>
            <td><a href="{{ route('own_form') }}">Own Form</a></td>
        </tr>
        <tr><td></td>
            <td><a href="{{ route('hosted_payment_laravel_pkg') }}">Hosted payment (Laravel package)</a></td>
            <td></td>
            <td></td>
        </tr>
        </table></br />
<div class="container">
    @yield('content')
</div>
   
</body>
</html>