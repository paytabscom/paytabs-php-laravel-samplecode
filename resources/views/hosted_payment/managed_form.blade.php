@extends('layout')

@section('content')

<script src="https://secure.paytabs.sa/payment/js/paylib.js"></script>

<form action="{{ route('process_managed_form') }}" id="payform" method="post">
  @csrf

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
  <input type="submit" value="Place order">
</form>

<script type="text/javascript">
var myform = document.getElementById('payform');
paylib.inlineForm({
  'key': 'CBKMMK-VBKM62-6PVNP6-RVTMHM',
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