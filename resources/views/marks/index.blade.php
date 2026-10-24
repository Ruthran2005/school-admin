@extends('layouts.app')
@section('title','Marks')
@section('content')
<div class="page-heading">
  <div>
    <p class="page-eyebrow">ACADEMICS</p>
    <h1 class="page-title">Marks</h1>
    <p class="page-description">Enter and update student marks for an exam subject.</p>
  </div>
</div>
<form method="GET" class="filter-panel">
  <div class="filter-field"><label for="marks-exam">Exam</label>
    <select id="marks-exam" name="exam" class="input" required><option value="">Select exam</option>
      @foreach($exams as $e)<option value="{{ $e->id }}" @selected($exam && $exam->id===$e->id)>{{ $e->name }} ({{ $e->schoolClass->label }})</option>@endforeach
    </select></div>
  <div class="filter-field"><label for="marks-subject">Subject</label>
    <select id="marks-subject" name="subject" class="input" required><option value="">Select subject</option>
      @foreach($subjects as $s)<option value="{{ $s->id }}" @selected($subject && $subject->id===$s->id)>{{ $s->name }}</option>@endforeach
    </select></div>
  <button class="btn" type="submit">Load students <span aria-hidden="true">→</span></button>
</form>
@if($exam && $subject)
<form method="POST" action="{{ route('staff.marks.store') }}" class="table-panel">
  @csrf <input type="hidden" name="exam_id" value="{{ $exam->id }}"><input type="hidden" name="subject_id" value="{{ $subject->id }}">
  <div class="table-panel-heading">
    <div><h2>{{ $exam->name }} <span class="heading-separator">·</span> {{ $subject->name }}</h2><p>{{ $students->count() }} students in this class</p></div>
    <div class="max-marks-field"><label for="max-marks">Max marks</label><input id="max-marks" type="number" name="max_marks" min="1" step="any" value="{{ $existing->first()->max_marks ?? 100 }}" class="input"></div>
  </div>
  @error('marks.*')<p class="text-red-600 text-sm mb-2">{{ $message }}</p>@enderror
  <div class="table-scroll"><table class="w-full">
    <thead><tr><th class="th">Roll</th><th class="th">Name</th><th class="th">Marks</th></tr></thead>
    @forelse($students as $s)
      <tr><td class="td">{{ $s->roll_no }}</td><td class="td">{{ $s->user->name }}</td>
      <td class="td"><label class="sr-only" for="mark-{{ $s->id }}">Marks for {{ $s->user->name }}</label><input id="mark-{{ $s->id }}" type="number" step="any" min="0" name="marks[{{ $s->id }}]" value="{{ $existing[$s->id]->marks_obtained ?? '' }}" class="input marks-input"></td></tr>
    @empty<tr><td class="empty-cell" colspan="3">No students found in this class.</td></tr>@endforelse
  </table></div>
  @if($students->count())<div class="form-panel-footer"><span class="text-xs text-slate-500">Make sure marks do not exceed the maximum score.</span><button class="btn" type="submit">Save marks</button></div>@endif
</form>
@endif
@endsection
