@extends('frontend.master')
@section('title', 'Client Registration | Buildwise')
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
            <h1>Register as a Client</h1>
            <p>Your account will be activated once an admin approves your registration. After that, you can log in and choose a flat from completed projects.</p>
            @if(session('notice'))
                <div class="alert alert-info">{{ session('notice') }}</div>
            @endif
            @if($errors->any())
                <div class="form-error">{{ $errors->first() }}</div>
            @endif
            <form method="post" action="{{ route('client.register') }}">
                @csrf

                <label for="full_name">Full Name<strong>*</strong></label>
                <input id="full_name" name="full_name" value="{{ old('full_name') }}" maxlength="100" required>

                <label for="email">Email<strong>*</strong></label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" maxlength="100" required>

                <label for="phone">Phone<strong>*</strong></label>
                <input id="phone" name="phone" value="{{ old('phone') }}" maxlength="20" required>

                <label for="password">Password <strong>*</strong>(at least 8 characters)</label>
                <input id="password" name="password" type="password" minlength="8" required>

                <label for="password_confirmation">Confirm Password<strong>*</strong></label>
                <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" required>

                <button class="button button-primary" type="submit" style="margin-top:20px">Submit Registration Request</button>
                <p style="margin-top:16px">Already registered? <a href="{{ route('login') }}">Log in</a></p>
            </form>
        </div></section>
@endsection
