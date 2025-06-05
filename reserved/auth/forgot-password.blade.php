@extends('layouts.app')
@section('content')
{{-- message --}}
{!! Toastr::message() !!}
<div class="login-right">
    <div class="login-right-wrap" style="max-height: 99vh; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #888 #f1f1f1;">
        <h1>Forgot Password</h1>
        <p class="account-subtitle">Enter your email address and we'll send you a link to reset your password.</p>
        
        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Email <span class="login-danger">*</span></label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                <span class="profile-views"><i class="fas fa-envelope"></i></span>
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit">Send Reset Link</button>
            </div>
            <div class="dont-have">Remember your password? <a href="{{ route('login') }}">Login</a></div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Add any necessary JavaScript here
</script>
@endpush

@endsection
