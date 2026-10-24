@extends('layouts.app')
@section('title','Change password')
@section('content')
<form method="POST" action="{{ route('password.update') }}" class="card max-w-md space-y-4">
  @csrf @method('PUT')
  <h1 class="text-lg font-semibold">Change password</h1>
  @foreach(['current_password'=>'Current password','password'=>'New password','password_confirmation'=>'Confirm new password'] as $n=>$l)
    <div><label class="text-sm">{{ $l }}</label><input type="password" name="{{ $n }}" class="input" required>
    @error($n)<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror</div>
  @endforeach
  <button class="btn">Update</button>
</form>
@endsection
