@extends('frontend.master')
@section('title', 'অনুমোদনের অপেক্ষায় | Buildwise')
@section('content')
<section style="width:min(700px,calc(100% - 36px));margin:60px auto;padding:32px;border:1px solid #dbe4d8;border-radius:14px;background:#fff">
    <p class="eyebrow">ক্লায়েন্ট অ্যাকাউন্ট</p><h1>অনুমোদনের অপেক্ষায়</h1>
    <p>আপনার নিবন্ধন Admin-এর কাছে পাঠানো হয়েছে। অনুমোদন হলে অ্যাকাউন্ট চালু হবে এবং আপনি ফ্ল্যাট বুকিং করতে পারবেন।</p>
    <a class="button button-primary" href="{{ url('/') }}">হোমপেজে ফিরুন</a>
</section>
@endsection
