@extends('layout')

@section('content')

<form  action="{{ route('verify_request') }}" method="POST">
    @csrf
    
    Request to test:<br> <input type="radio" name="type" value="callback" />callback<br /> 
    <input type="radio" name="type" value="return" />return<br /><br />
    
    Signature<br><input name="signature" size="70" /><br /><br />
    Request Content<br><textarea name="content" style="width: 750px; height: 200px;"></textarea><br /><br />
    <input type="submit" value="Verify" />
</form>
    
</form>

@endsection