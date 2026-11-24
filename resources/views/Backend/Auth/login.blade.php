<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark min-vh-100 d-flex align-items-center">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-sm-5">
                <div class="text-center mb-4">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 mb-2">Construction Management</span>
                    <h1 class="h4 fw-bold mb-1">Dashboard Login</h1>
                    <p class="text-secondary small mb-0">Sign in to your account</p>
                </div>

                @if(session('registration_pending'))
                    <div class="alert alert-success small py-2">{{ session('registration_pending') }}</div>
                @endif
                @if(session('pendingApproval'))
                    <div class="alert alert-warning small py-2">{{ session('pendingApproval') }}</div>
                @endif
                @if(session('failed'))
                    <div class="alert alert-danger small py-2">{{ session('failed') }}</div>
                @endif

                <form action="{{ route('doLogin') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control bg-light border-0 py-2" placeholder="name@example.com" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="pwd" class="form-control bg-light border-0 py-2" placeholder="••••••••" required>
                            <button class="btn bg-light border-0 text-secondary" type="button" onclick="togglePwd()" aria-label="Toggle password">
                                <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 rounded-3 fw-semibold py-2 mt-2">Login</button>
                </form>

                <div class="text-center mt-4 pt-3 border-top small">
                    <a href="{{ route('client.register.form') }}" class="text-decoration-none fw-semibold">ক্লায়েন্ট হিসেবে নতুন নিবন্ধন</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePwd() {
        const pwd = document.getElementById('pwd');
        pwd.type = pwd.type === 'password' ? 'text' : 'password';
    }
</script>
</body>
</html>
