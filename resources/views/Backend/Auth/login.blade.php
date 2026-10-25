<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>

    <link href="{{ asset('Backend/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="{{ asset('Backend/css/sb-admin-2.min.css') }}" rel="stylesheet">
</head>
<body class="bg-gradient-primary">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-8 col-md-8">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="p-5">

                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Admin Login</h1>
                                </div>

                                <form class="user" action="{{ route('doLogin') }}" method="post">
                                    @csrf

                                    <div class="form-group">
                                        <input type="email" name="email"
                                               value="{{ old('email') }}"
                                               class="form-control form-control-user"
                                               id="exampleInputEmail"
                                               placeholder="Email...">
                                    </div>

                                    <div class="form-group">
                                        <input type="password" name="password"
                                               class="form-control form-control-user"
                                               id="exampleInputPassword"
                                               placeholder="Password...">
                                    </div>
                                    @if(session('failed'))
                                        <span class="text-danger text-center">{{ session('failed') }}</span>
                                    @endif

                                    <button type="submit" class="btn btn-primary btn-user btn-block">Login</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('Backend/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('Backend/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('Backend/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('Backend/js/sb-admin-2.min.js') }}"></script>

</body>
</html>