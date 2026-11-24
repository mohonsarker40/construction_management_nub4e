@extends('frontend.master')
@section('title', 'Request is Pending | Buildwise')
@section('content')
<section style="width:min(700px,calc(100% - 36px));margin:60px auto;padding:32px;border:1px solid #dbe4d8;border-radius:14px;background:#fff">
    <p class="eyebrow">Client Account</p>
    <h1>Request is Pending</h1>
    <p>Your request sended to Admin. After approval you can login and buy Flats</p>
    <a class="button button-primary" href="{{ url('/') }}">Back to Homepage</a>
</section>
@endsection
