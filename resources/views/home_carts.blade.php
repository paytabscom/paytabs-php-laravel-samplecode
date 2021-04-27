@extends('layout')

@section('content')
<table border=1 cellspacing="5" cellpadding="5" style="border: 1px solid blue; padding: 15px;">
    <tr style="font-weight: bold;"><td>ID</td><td>products</td><td>cart amount</td><td>payment status</td></tr>
    @foreach ($carts as $cart)
    <tr>
        <td>{{ $cart->cart_id }}</td>
        <td>@php
            $products= unserialize($cart->products);
            foreach($products as $product){
                echo $product. "<br />";
            }
        @endphp</td>
        <td>{{ $cart->total }}</td>
        <td>{{ $cart->payment_status }}</td>
    @endforeach
    <tr>
        <td></td>
    </tr>
</table>
@endsection