<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sign in · School ERP</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body">
  <main class="login-page">
    <section class="login-showcase" aria-label="School ERP">
      <a class="login-brand" href="{{ route('login') }}">
        <span class="brand-mark" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none"><path d="M3 9.5 12 4l9 5.5v9a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18.5v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 20v-6h8v6M8 9h.01M12 9h.01M16 9h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </span>
        <span><strong>School ERP</strong><small>ADMIN PORTAL</small></span>
      </a>
      <div class="login-message">
        <span class="eyebrow">A BETTER WAY TO RUN YOUR SCHOOL</span>
        <h1>Everything in one place.</h1>
        <p>Manage your school day with clarity. Students, academics, attendance and fees — all connected in one simple workspace.</p>
      </div>
      <p class="login-caption">Secure access for authorized school administrators.</p>
    </section>

    <section class="login-panel">
      <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf
        <h2>Welcome back</h2>
        <p class="login-subtitle">Sign in to continue to your admin dashboard.</p>

        @if($errors->any())
          <div class="flash flash-error" role="alert">{{ $errors->first() }}</div>
        @endif

        <div class="mb-5">
          <label for="email">Email address</label>
          <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@school.edu" class="input">
        </div>
        <div class="mb-4">
          <label for="password">Password</label>
          <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" class="input">
        </div>
        <label class="remember-row mb-5"><input type="checkbox" name="remember" value="1"> Remember me</label>
        <button class="btn login-submit" type="submit">Sign in to your account <span aria-hidden="true">→</span></button>
      </form>
    </section>
  </main>
</body>
</html>
