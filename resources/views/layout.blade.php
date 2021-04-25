<!DOCTYPE html>
<html>
<head>
    <title>Laravel 8 CRUD Application - laravelcode.com</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">
</head>
<body>
    <table border=1 cellspacing="5" cellpadding="5" style="border: 1px solid blue; padding: 15px; margin: 15px;">
        <tr> <td><a href="?">home</a></td>
            <td><a href="{{ route('initiate_hosted_payment') }}">Create Hosted Payment Page</a></td>
            <td><a href="{{ route('collect_request_details') }}">Verify a callback or a return</a></td>
            <td><a href="{{ route('managed_form') }}">Managed Form</a></td>
        </tr></table></br />
  
<div class="container">
    @yield('content')
</div>
   
</body>
</html>