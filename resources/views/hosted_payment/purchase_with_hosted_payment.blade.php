@extends('layout')

@section('content')

<b>Select items to purchase:</b><br />
<form  action="" method="POST">
    @csrf
    <input type="checkbox" name="products[0]" value="0.5" /> product #0 (0.5 SAR)<br />
    <input type="checkbox" name="products[1]" value="1" /> product #1 (1 SAR)<br />
    <input type="checkbox" name="products[2]" value="5" /> product #2 (5 SAR)<br />
    <input type="checkbox" name="products[3]" value="7" /> product #3 (7 SAR)<br />
    <br />
    <input type="checkbox" name="framed" value="true" /> <b>display payment within this website (Framed Payment)</b>
    <br />
    <input type="submit" value="Purchase" name="sale" /> &nbsp; &nbsp; &nbsp;
    <input type="submit" value="Delayed Purchase (Auth)" name="auth" />
</form>

@endsection