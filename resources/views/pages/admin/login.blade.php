@extends('layouts.admin', ['title' => 'Claire Stefanich Arts | Admin Login'])

@section('content')
<section class="kimi-login-screen">
    <form method="post" action="{{ route('admin.authenticate') }}" class="kimi-login-card">
        @csrf
        <div class="kimi-login-icon" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <h1>Admin Panel</h1>
        <p>Claire Stefanich Studio</p>

        <label for="admin-password">Password</label>
        <div class="kimi-password-wrap">
            <input id="admin-password" type="password" name="password" placeholder="Enter admin password" required>
            <button type="button" data-password-toggle aria-label="Show or hide password">👁</button>
        </div>
        @error('password')<p class="error-text">{{ $message }}</p>@enderror
        <button type="submit" class="kimi-gradient-button full">Sign In</button>
    </form>
</section>
@endsection
