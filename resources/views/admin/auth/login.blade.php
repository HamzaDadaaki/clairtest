@extends('layouts.admin', ['title' => 'Admin Login | Afayar'])

@section('content')
<section class="auth-card">
    <div class="auth-copy">
        <span class="eyebrow">Secure access</span>
        <h1>Login to the Afayar admin panel</h1>
        <p>Use the admin email and password to manage products, product images, descriptions, and customer reviews.</p>
        <div class="auth-tip">
            <strong>Admin URL</strong>
            <span>{{ url('/admin/login') }}</span>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.login.store') }}" class="auth-form">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            @error('email')
                <small class="field-error">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>

        <button type="submit" class="admin-primary-button full">Login</button>
    </form>
</section>
@endsection
