<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'School ERP') · Admin</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-body">
@php
  $role = auth()->user()->role;
  $navigation = [
      'Overview' => [
          ['Dashboard', 'dashboard', 'dashboard', null],
      ],
      'School management' => [
          ['Students', 'admin.students.index', 'students', 'students'],
          ['Teachers', 'admin.teachers.index', 'teachers', 'teachers'],
          ['Classes', 'admin.classes.index', 'classes', 'classes'],
          ['Subjects', 'admin.subjects.index', 'subjects', 'subjects'],
          ['Timetable', 'admin.timetable.index', 'timetable', 'timetable'],
      ],
      'Academics' => [
          ['Attendance', 'staff.attendance', 'attendance', 'attendance'],
          ['Exams', 'admin.exams.index', 'exams', 'exams'],
          ['Marks', 'staff.marks', 'marks', 'marks'],
      ],
      'Finance' => [
          ['Fee structures', 'admin.fee-structures.index', 'fees', 'fees'],
          ['Invoices', 'admin.invoices.index', 'invoices', 'invoices'],
      ],
      'Administration' => [
          ['Admin users', 'admin.admins.index', 'admins', 'admins'],
      ],
  ];
  $navigation = array_filter(array_map(
      fn ($links) => array_values(array_filter($links, fn ($link) => $link[3] === null || auth()->user()->hasModuleAccess($link[3]))),
      $navigation,
  ));
  $initials = collect(explode(' ', auth()->user()->name))
      ->filter()
      ->take(2)
      ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
      ->implode('');
@endphp
<div class="app-shell">
  <aside class="sidebar noprint">
    <a class="brand" href="{{ route('dashboard') }}">
      <span class="brand-mark" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none"><path d="M3 9.5 12 4l9 5.5v9a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18.5v-9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 20v-6h8v6M8 9h.01M12 9h.01M16 9h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
      </span>
      <span class="brand-copy"><strong>School ERP</strong><small>ADMIN PORTAL</small></span>
    </a>

    <nav class="sidebar-nav" aria-label="Main navigation">
      @foreach($navigation as $group => $links)
        <div class="nav-group">
          <p class="nav-heading">{{ $group }}</p>
          @foreach($links as [$label, $routeName, $key, $module])
            @php $isActive = request()->routeIs($routeName) || ($key === 'exams' && request()->routeIs('admin.exams.results')); @endphp
            <a href="{{ route($routeName) }}" @class(['nav-link', 'is-active' => $isActive]) @if($isActive) aria-current="page" @endif>
              <span class="nav-icon" aria-hidden="true">{{ mb_substr($label, 0, 1) }}</span>
              <span>{{ $label }}</span>
            </a>
          @endforeach
        </div>
      @endforeach
    </nav>

    <div class="sidebar-footer">
      <span class="status-dot"></span>
      <span>School management<br><small>All systems operational</small></span>
    </div>
  </aside>

  <div class="app-main">
    <header class="topbar noprint">
      <div class="topbar-context">
        <span class="topbar-kicker">SCHOOL MANAGEMENT</span>
        <span class="topbar-divider">/</span>
        <span class="topbar-page">@yield('title', 'Dashboard')</span>
      </div>
      <div class="topbar-actions">
        <a href="{{ route('password.edit') }}" class="account-link">Change password</a>
        <span class="user-avatar" aria-hidden="true">{{ $initials }}</span>
        <div class="user-meta"><strong>{{ auth()->user()->name }}</strong><small>{{ ucfirst($role) }}</small></div>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn btn-quiet btn-sm" type="submit">Sign out</button>
        </form>
      </div>
    </header>

    <main class="content-area">
      @if(session('ok'))
        <div class="flash flash-success noprint" role="status">{{ session('ok') }}</div>
      @endif
      @if(session('err'))
        <div class="flash flash-error noprint" role="alert">{{ session('err') }}</div>
      @endif
      @if($errors->any())
        <div class="flash flash-error noprint" role="alert">
          <strong>Please review the following:</strong>
          <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
      @endif
      @yield('content')
    </main>
  </div>
</div>
</body>
</html>
