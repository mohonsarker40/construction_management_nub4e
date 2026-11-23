@extends('frontend.master')
@section('title', 'ক্লায়েন্ট নিবন্ধন | Buildwise')
@section('styles')
    .register-wrap { width:min(680px,calc(100% - 36px)); margin:50px auto 70px; }
    .register-card { padding:clamp(22px,5vw,42px); border:1px solid var(--line); border-radius:16px; background:#fff; box-shadow:0 14px 44px rgba(24,61,50,.08); }
    .register-card h1 { margin:0 0 8px; color:var(--green); }
    .register-card label { display:block; margin:14px 0 6px; font-weight:700; }
    .register-card input { width:100%; padding:12px; border:1px solid #cbd4ce; border-radius:7px; font:inherit; }
    .form-error { color:#a42424; }
@endsection
@section('content')
<section class="register-wrap"><div class="register-card">
    <p class="eyebrow">নতুন ক্রেতা</p><h1>ক্লায়েন্ট হিসেবে নিবন্ধন</h1>
    <p>নিবন্ধনের পর Admin অনুমোদন করলে অ্যাকাউন্ট চালু হবে। এরপর লগইন করে সম্পন্ন প্রজেক্টের ফ্ল্যাট বাছতে পারবেন।</p>
    @if(session('notice'))<div class="alert alert-info">{{ session('notice') }}</div>@endif
    @if($errors->any())<div class="form-error">{{ $errors->first() }}</div>@endif
    <form method="post" action="{{ route('client.register') }}">@csrf
        <label for="full_name">পূর্ণ নাম</label><input id="full_name" name="full_name" value="{{ old('full_name') }}" maxlength="100" required>
        <label for="email">ইমেইল</label><input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="100" required>
        <label for="phone">ফোন</label><input id="phone" name="phone" value="{{ old('phone') }}" maxlength="20" required>
        <label for="password">পাসওয়ার্ড (কমপক্ষে ৮ অক্ষর)</label><input id="password" name="password" type="password" minlength="8" required>
        <label for="password_confirmation">পাসওয়ার্ড আবার লিখুন</label><input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required>
        <button class="button button-primary" type="submit" style="margin-top:20px">নিবন্ধনের অনুরোধ পাঠান</button>
        <p style="margin-top:16px">আগে নিবন্ধন করেছেন? <a href="{{ route('login') }}">লগইন করুন</a></p>
    </form>
</div></section>
@endsection
