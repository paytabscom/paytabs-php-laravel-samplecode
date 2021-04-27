<!DOCTYPE html>
<html>
<head>
    <title>Laravel 8 CRUD Application - laravelcode.com</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">
</head>
<body>
    <table border=1 cellspacing="5" cellpadding="5" style="border: 1px solid blue; padding: 15px; margin: 15px;">
        <tr> <td><a href="{{ route('index') }}">Home</a></td>
            <td><a href="{{ route('purchase_with_hosted_payment') }}">Purchase with hosted payment</a></td>
            <td><a href="{{ route('managed_form') }}">Managed Form</a></td>
        <tr><td><a href="{{ route('collect_request_details') }}">Verify callback, return or IPN request</a></td> <td></td> <td></td></tr>
        </tr></table></br />
<div class="container">
    @yield('content')
</div>
   
</body>
</html>