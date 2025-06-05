<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login - IPMS</title>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        <style>
            body {
                background-color: #f8f9fa;
                min-height: 100vh;
                display: flex;
                align-items: center;
            }
            .login-container {
                max-width: 400px;
                width: 100%;
                padding: 40px;
                background: #fff;
                border-radius: 15px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            }
            .login-header {
                text-align: center;
                margin-bottom: 30px;
            }
            .login-header i {
                font-size: 2.5rem;
                color: #0d6efd;
                margin-bottom: 15px;
            }
            .login-header h1 {
                font-size: 1.75rem;
                font-weight: 600;
                color: #212529;
                margin-bottom: 10px;
            }
            .login-header p {
                color: #6c757d;
                margin: 0;
            }
            .form-floating {
                margin-bottom: 20px;
            }
            .form-floating > .form-control {
                height: 55px;
                padding: 1rem 0.75rem;
            }
            .form-floating > label {
                padding: 1rem 0.75rem;
            }
            .btn-login {
                height: 55px;
                font-weight: 500;
                width: 100%;
            }
            .divider {
                display: flex;
                align-items: center;
                text-align: center;
                margin: 25px 0;
                color: #6c757d;
            }
            .divider::before,
            .divider::after {
                content: '';
                flex: 1;
                border-bottom: 1px solid #dee2e6;
            }
            .divider span {
                padding: 0 15px;
            }
            .back-home {
                text-align: center;
                margin-top: 20px;
            }
            .back-home a {
                color: #6c757d;
                text-decoration: none;
                transition: color 0.3s ease;
            }
            .back-home a:hover {
                color: #0d6efd;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-4">
                    <div class="login-container">
                        <div class="login-header">
                            <i class="bi bi-briefcase"></i>
                            <h1>Welcome Back</h1>
                            <p>Sign in to continue to IPMS</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-floating">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                    id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                                <label for="email">Email address</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-floating">
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                    id="password" name="password" placeholder="Password" required>
                                <label for="password">Password</label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-decoration-none">Forgot password?</a>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary btn-login">
                                Sign In
                            </button>

                            <div class="divider">
                                <span>or</span>
                            </div>

                            <div class="back-home">
                                <a href="{{ url('/') }}">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Home
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html> 