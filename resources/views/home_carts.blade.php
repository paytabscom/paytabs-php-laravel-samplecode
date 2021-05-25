@extends('layout')

@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<h1>{!! $cart_profile !!}</h1>
<table border=1 cellspacing="5" cellpadding="5" style="border: 1px solid blue; padding: 15px;">
    <tr style="font-weight: bold;"><td>ID</td>
        <td>products</td>
        <td>cart amount</td>
        <td>tran type</td>
        <td>payment status</td>
        <td>capture status</td></tr>
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
        <td>{{ $cart->tran_type }}</td>
        <td>{{ $cart->payment_resp_status }}</td>
        <td>{{ $cart->capture_resp_status }}</td>
        <td>
        @if ($cart->payment_tran_ref)
            <a href="{{ route('capture', ['cartId' => $cart->cart_id] ) }}">capture tran</a> 
            | <a href="{{ route('void', ['cartId' => $cart->cart_id] ) }}">void tran</a> 
            | <a href="{{ route('refund', ['cartId' => $cart->cart_id] ) }}">refund tran</a> 
            | <a href="{{ route('lookup', ['cartId' => $cart->cart_id] ) }}" class="lookup">lookup tran</a>
        @else
            callback action needed to set tran ref
        @endif
        </td>
    @endforeach

</table>

<script type="text/javascript">
    $(document).ready(function(){
        $(".lookup").on( 'click', function (){
            url= $(this).attr('href');
            $.ajax({
                type: "GET",
                dataType: " json",
                url: url,
                success: function(data)
                {
                    alert('Tran status: '+ data.status+ "\n Tran code: "+ data.code+  "\n Tran message: "+ data.message);
                },
                error: function(jqXHR)
                {
                    alert(jqXHR.responseText);
                }
            });
            return false;
        });                  
    });
</script>

@endsection