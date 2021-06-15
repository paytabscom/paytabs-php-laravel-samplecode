@extends('layout')

@section('content')

<form action="{{ route('process_own_form') }}" id="payform" method="post">
  @csrf
  <b>Select items to purchase:</b><br />
    <input type="checkbox" name="products[0]" value="0.5" /> product #0 (0.5 SAR)<br />
    <input type="checkbox" name="products[1]" value="1" /> product #1 (1 SAR)<br />
    <input type="checkbox" name="products[2]" value="5" /> product #2 (5 SAR)<br />
    <input type="checkbox" name="products[3]" value="7" /> product #3 (7 SAR)<br />
    <br />
    
    <b>Card details:</b><br />
  <span id="paymentErrors"></span>
  <div class="row">
    <label>Card Number</label>
    <input type="text" name="number" size="20">
  </div>
  <div class="row">
    <label>Expiry Date (MM/YYYY)</label>
    <input type="text" name="expmonth" size="2">
    <input type="text" name="expyear" size="4">
  </div>
  <div class="row">
    <label>Security Code</label>
    <input type="text" name="cvv" size="4">
  </div>
  <input type="submit" value="Place order" name="sale" > &nbsp; &nbsp; &nbsp;
    <input type="submit" value="Delayed Purchase (Auth)" name="auth" />
</form>

@endsection