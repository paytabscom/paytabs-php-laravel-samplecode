@extends('layout')

@section('content')

<script src="{!! $js_lib_uri !!}js/paylib.js"></script>

<form action="{{ route('process_managed_form') }}" id="payform" method="post">
  @csrf
  <b>Select items to purchase:</b><br />
    <input type="checkbox" name="products[1]" value="1" /> product #1 (1 SAR)<br />
    <input type="checkbox" name="products[2]" value="5" /> product #2 (5 SAR)<br />
    <input type="checkbox" name="products[3]" value="7" /> product #3 (7 SAR)<br />
    <br />
    
    <b>Card details:</b><br />
  <span id="paymentErrors"></span>
  <div class="row">
    <label>Card Number</label>
    <input type="text" data-paylib="number" size="20">
  </div>
  <div class="row">
    <label>Expiry Date (MM/YYYY)</label>
    <input type="text" data-paylib="expmonth" size="2">
    <input type="text" data-paylib="expyear" size="4">
  </div>
  <div class="row">
    <label>Security Code</label>
    <input type="text" data-paylib="cvv" size="4">
  </div>
  <div><input type="checkbox" value="1" name="auth" /> Delayed Purchase (Auth)</div>
  <input type="submit" value="Place order" name="sale" > &nbsp; &nbsp; &nbsp;
</form>

<script type="text/javascript">
var myform = document.getElementById('payform');
paylib.inlineForm({
  'key': '{!! $client_key !!}',
  'form': myform,
  'autosubmit': true,
  'callback': function(response) {
    document.getElementById('paymentErrors').innerHTML = '';
    if (response.error) {             
      paylib.handleError(document.getElementById('paymentErrors'), response); 
    }
  }
});
</script>

@endsection