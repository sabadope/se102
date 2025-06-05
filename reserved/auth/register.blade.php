@extends('layouts.app')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
    <div class="login-right">
        <div class="login-right-wrap" style="max-height: 99vh; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #888 #f1f1f1;">
            <h1>Welcome to Sign Up</h1>
            <p class="account-subtitle">Enter details to create your account</p>
            <form action="{{ route('register') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Full Name <span class="login-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                    <span class="profile-views"><i class="fas fa-user-circle"></i></span>
                </div>
                <div class="form-group">
                    <label>Email <span class="login-danger">*</span></label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                    <span class="profile-views"><i class="fas fa-envelope"></i></span>
                </div>
                {{-- insert defaults --}}
                <input type="hidden" class="image" name="image" value="photo_defaults.jpg">
                <div class="form-group local-forms">
                    <label>Role Name <span class="login-danger">*</span></label>
                    <select class="form-control select" name="role_name" id="role_name">
                        <option selected disabled>Role Type</option>
                        @foreach ($role as $name)
                            <option value="{{ $name->role_type }}" 
                                {{ old('role_name') == $name->role_type ? 'selected' : '' }}
                                {{ $name->role_type == 'Admin' ? 'disabled' : '' }}
                                {{ $name->role_type == 'Admin' ? 'style=color:#999;' : '' }}>
                                {{ $name->role_type }}
                                @if($name->role_type == 'Admin')
                                    (Already exists)
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Password <span class="login-danger">*</span></label>
                    <input type="password" class="form-control pass-input" name="password" id="password">
                    <span class="profile-views feather-eye toggle-password"></span>
                    @if(session('password_errors'))
                    <div class="password-requirements mt-2">
                        <small class="text-muted">Password must contain:</small>
                        <ul class="list-unstyled mt-1" style="font-size: 0.85rem;">
                            <li id="length" class="requirement-item {{ in_array('Password must be at least 8 characters long.', session('password_errors')) ? 'text-danger' : 'text-success' }}">
                                <strong>{{ in_array('Password must be at least 8 characters long.', session('password_errors')) ? '✗' : '✓' }} At least 8 characters</strong>
                            </li>
                            <li id="uppercase" class="requirement-item {{ in_array('Password must contain at least one uppercase letter.', session('password_errors')) ? 'text-danger' : 'text-success' }}">
                                <strong>{{ in_array('Password must contain at least one uppercase letter.', session('password_errors')) ? '✗' : '✓' }} One uppercase letter</strong>
                            </li>
                            <li id="lowercase" class="requirement-item {{ in_array('Password must contain at least one lowercase letter.', session('password_errors')) ? 'text-danger' : 'text-success' }}">
                                <strong>{{ in_array('Password must contain at least one lowercase letter.', session('password_errors')) ? '✗' : '✓' }} One lowercase letter</strong>
                            </li>
                            <li id="number" class="requirement-item {{ in_array('Password must contain at least one number.', session('password_errors')) ? 'text-danger' : 'text-success' }}">
                                <strong>{{ in_array('Password must contain at least one number.', session('password_errors')) ? '✗' : '✓' }} One number</strong>
                            </li>
                            <li id="special" class="requirement-item {{ in_array('Password must contain at least one special character (@$!%*?&).', session('password_errors')) ? 'text-danger' : 'text-success' }}">
                                <strong>{{ in_array('Password must contain at least one special character (@$!%*?&).', session('password_errors')) ? '✗' : '✓' }} One special character (@$!%*?&)</strong>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>
                <div class="form-group">
                    <label>Confirm password <span class="login-danger">*</span></label>
                    <input type="password" class="form-control pass-confirm" name="password_confirmation" id="password_confirmation">
                    <span class="profile-views feather-eye reg-toggle-password"></span>
                </div>
                <div class=" dont-have">Already Registered? <a href="{{ route('login') }}">Login</a></div>
                <div class="form-group mb-0">
                    <button class="btn btn-primary btn-block" type="submit">Register</button>
                </div>
            </form>
            <div class="login-or">
                <span class="or-line"></span>
                <span class="span-or">or</span>
            </div>
            <div class="social-login">
                <a href="#"><i class="fab fa-google-plus-g"></i></a>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const passwordInput = document.getElementById('password');
        const requirements = {
            length: document.getElementById('length'),
            uppercase: document.getElementById('uppercase'),
            lowercase: document.getElementById('lowercase'),
            number: document.getElementById('number'),
            special: document.getElementById('special')
        };

        function updateRequirement(element, isValid) {
            if (isValid) {
                element.innerHTML = '<strong>✓ ' + element.innerHTML.substring(2) + '</strong>';
                element.style.color = '#28a745';
                element.style.fontWeight = 'bold';
            } else {
                element.innerHTML = '<strong>✗ ' + element.innerHTML.substring(2) + '</strong>';
                element.style.color = '#dc3545';
                element.style.fontWeight = 'bold';
            }
        }

        // Only show real-time validation if there were previous validation errors
        const hasValidationErrors = {{ session('password_errors') ? 'true' : 'false' }};
        
        if (hasValidationErrors) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                
                // Check length
                updateRequirement(requirements.length, password.length >= 8);
                
                // Check uppercase
                updateRequirement(requirements.uppercase, /[A-Z]/.test(password));
                
                // Check lowercase
                updateRequirement(requirements.lowercase, /[a-z]/.test(password));
                
                // Check number
                updateRequirement(requirements.number, /[0-9]/.test(password));
                
                // Check special character
                updateRequirement(requirements.special, /[@$!%*?&]/.test(password));
            });
        }
    </script>
    <style>
        .login-right-wrap {
            padding-right: 15px;
        }
        .login-right-wrap::-webkit-scrollbar {
            width: 4px;
        }
        .login-right-wrap::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
            margin-left: 15px;
        }
        .login-right-wrap::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
            margin-left: 15px;
        }
        .login-right-wrap::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .password-requirements {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .requirement-item {
            margin-bottom: 5px;
            transition: all 0.3s ease;
        }
        .text-danger {
            color: #dc3545 !important;
            font-weight: bold !important;
        }
        .text-success {
            color: #28a745 !important;
            font-weight: bold !important;
        }
    </style>
    @endpush
@endsection
