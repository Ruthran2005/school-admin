@extends('layouts.app')
@section('title','Report card')
@section('content')
<div class="card max-w-2xl mx-auto">
  <div class="text-center border-b pb-3 mb-3"><h1 class="text-xl font-bold">Report Card</h1><p class="text-sm text-gray-500">{{ $exam->name }}</p></div>
  <div class="grid grid-cols-2 gap-2 text-sm mb-4">
    <div>Name: <b>{{ $student->user->name }}</b></div><div>Class: <b>{{ $student->schoolClass->label }}</b></div>
    <div>Admission no: <b>{{ $student->admission_no }}</b></div><div>Roll no: <b>{{ $student->roll_no }}</b></div>
  </div>
  <table class="w-full">
    <thead><tr><th class="th">Subject</th><th class="th">Marks</th><th class="th">Out of</th><th class="th">Grade</th></tr></thead>
    @forelse($marks as $m)
      <tr><td class="td">{{ $m->subject->name }}</td><td class="td">{{ rtrim(rtrim(number_format($m->marks_obtained,2),'0'),'.') }}</td>
      <td class="td">{{ rtrim(rtrim(number_format($m->max_marks,2),'0'),'.') }}</td>
      <td class="td">{{ \App\Models\Mark::grade($m->max_marks ? $m->marks_obtained/$m->max_marks*100 : 0) }}</td></tr>
    @empty<tr><td class="td text-gray-500" colspan="4">Marks not published yet.</td></tr>@endforelse
    @if($marks->count())
    <tr class="bg-gray-50 font-semibold"><td class="td">Total</td><td class="td">{{ $total+0 }}</td><td class="td">{{ $max+0 }}</td><td class="td">{{ $pct }}% · {{ $grade }}</td></tr>
    @endif
  </table>
  <p class="text-sm mt-3">Attendance: <b>{{ $attendance !== null ? $attendance.'%' : 'N/A' }}</b></p>
  <button onclick="window.print()" class="btn mt-4 noprint">Print</button>
</div>
@endsection
