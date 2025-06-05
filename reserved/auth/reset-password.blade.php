@extends('layouts.app')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="login-right">
    <div class="login-right-wrap" style="max-height: 99vh; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #888 #f1f1f1;">
        <h1>Reset Password</h1>
        <p class="account-subtitle">Enter your new password below.</p>
        
        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <div class="form-group">
                <label>Email <span class="login-danger">*</span></label>
                <input type="email" class="form-control" name="email" value="{{ $email ?? old('email') }}" required>
                <span class="profile-views"><i class="fas fa-envelope"></i></span>
            </div>

            <div class="form-group">
                <label>New Password <span class="login-danger">*</span></label>
                <input type="password" class="form-control pass-input" name="password" required>
                <span class="profile-views feather-eye toggle-password"></span>
            </div>

            <div class="form-group">
                <label>Confirm Password <span class="login-danger">*</span></label>
                <input type="password" class="form-control pass-confirm" name="password_confirmation" required>
                <span class="profile-views feather-eye reg-toggle-password"></span>
            </div>

            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit">Reset Password</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Toggle password visibility
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const passwordInput = document.querySelector('.pass-input');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('feather-eye');
        this.classList.toggle('feather-eye-off');
    });

    document.querySelector('.reg-toggle-password').addEventListener('click', function() {
        const passwordInput = document.querySelector('.pass-confirm');
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('feather-eye');
        this.classList.toggle('feather-eye-off');
    });
</script>
@endpush

@endsection
