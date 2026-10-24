@extends('layouts.app')
@section('title','Attendance')
@section('content')
<div class="page-heading">
  <div>
    <p class="page-eyebrow">ACADEMICS</p>
    <h1 class="page-title">Attendance</h1>
    <p class="page-description">Record and review student attendance by class and date.</p>
  </div>
</div>
<form method="GET" class="filter-panel">
  <div class="filter-field"><label for="attendance-class">Class</label>
    <select id="attendance-class" name="class" class="input" required><option value="">Select class</option>
      @foreach($classes as $c)<option value="{{ $c->id }}" @selected($class && $class->id===$c->id)>{{ $c->label }}</option>@endforeach
    </select></div>
  <div class="filter-field"><label for="attendance-date">Date</label><input id="attendance-date" type="date" name="date" value="{{ $date }}" max="{{ today()->toDateString() }}" class="input"></div>
  <button class="btn" type="submit">Load students <span aria-hidden="true">→</span></button>
</form>
@if($class)
<form method="POST" action="{{ route('staff.attendance.store') }}" class="table-panel">
  @csrf <input type="hidden" name="class" value="{{ $class->id }}"><input type="hidden" name="date" value="{{ $date }}">
  <div class="table-panel-heading">
    <div><h2>{{ $class->label }} <span class="heading-separator">·</span> {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h2><p>{{ $students->count() }} students in this class</p></div>
    <button type="button" class="btn btn-sm btn-quiet" onclick="this.form.querySelectorAll('input[type=radio][value=present]').forEach(r=>r.checked=true)">Mark all present</button>
  </div>
  <div class="table-scroll"><table class="w-full">
    <thead><tr><th class="th">Roll</th><th class="th">Name</th><th class="th">Present</th><th class="th">Absent</th><th class="th">Late</th></tr></thead>
    @forelse($students as $s)
      @php $cur = $existing[$s->id] ?? 'present'; @endphp
      <tr><td class="td">{{ $s->roll_no }}</td><td class="td">{{ $s->user->name }}</td>
      @foreach(['present','absent','late'] as $st)
        <td class="td status-choice"><label class="sr-only" for="attendance-{{ $s->id }}-{{ $st }}">{{ ucfirst($st) }} for {{ $s->user->name }}</label><input id="attendance-{{ $s->id }}-{{ $st }}" type="radio" name="status[{{ $s->id }}]" value="{{ $st }}" @checked($cur===$st)></td>
      @endforeach</tr>
    @empty<tr><td class="empty-cell" colspan="5">No students found in this class.</td></tr>@endforelse
  </table></div>
  @if($students->count())<div class="form-panel-footer"><span class="text-xs text-slate-500">Review attendance before saving.</span><button class="btn" type="submit">Save attendance</button></div>@endif
</form>
@endif
@endsection
