@extends('layout')

@section('content')

<form  action="{{ route('do_hosted_payment') }}" method="POST">
    @csrf
    Select items to purchase:<br />
    <input type="checkbox" name="products[1]" value="1" /> product #1 (1 SAR)<br />
    <input type="checkbox" name="products[2]" value="5" /> product #2 (5 SAR)<br />
    <input type="checkbox" name="products[3]" value="7" /> product #3 (7 SAR)<br />
    <br />
    <input type="submit" value="Purchase" />
</form>

@endsection