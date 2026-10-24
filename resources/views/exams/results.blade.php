@extends('layouts.app')
@section('title','Report cards')
@section('content')
<h1 class="text-xl font-semibold mb-1">Report cards · {{ $exam->name }}</h1>
<p class="text-sm text-gray-500 mb-4">{{ $exam->schoolClass->label }}</p>
<div class="bg-white rounded shadow"><table class="w-full">
  @forelse($students as $s)
    <tr><td class="td">{{ $s->roll_no }}</td><td class="td">{{ $s->user->name }}</td>
    <td class="td text-right"><a class="btn btn-sm" href="{{ route('report.card',[$s,$exam]) }}">View report card</a></td></tr>
  @empty<tr><td class="td text-gray-500">No students.</td></tr>@endforelse
</table></div>
@endsection
