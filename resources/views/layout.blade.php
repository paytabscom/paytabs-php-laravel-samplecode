<!DOCTYPE html>
<html>
<head>
    <title>Laravel 8 CRUD Application - laravelcode.com</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">
</head>
<body>
    <table border=1 cellspacing="5" cellpadding="5" style="border: 1px solid blue; padding: 15px; margin: 15px;">
        <tr> <td><a href="?">home</a></td>
            <td><a href="{{ route('initiate_hosted_payment') }}">create hosted payment page</a></td>
            <td><a href="{{ route('collect_request_details') }}">verify a callback or a return</a></td>
        </tr></table></br />
  
<div class="container">
    @yield('content')
</div>
   
</body>
</html>