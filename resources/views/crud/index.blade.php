@extends('layouts.app')
@section('title', $title)
@section('content')
<div class="page-heading">
  <div>
    <p class="page-eyebrow">RECORDS</p>
    <h1 class="page-title">{{ $title }}</h1>
    <p class="page-description">Manage and review your school {{ strtolower($title) }}.</p>
  </div>
  <a href="{{ route($route.'.create') }}" class="btn"><span aria-hidden="true">+</span> Add {{ \Illuminate\Support\Str::singular($title) }}</a>
</div>
<section class="table-panel">
  <div class="table-panel-heading">
    <div>
      <h2>{{ $title }} list</h2>
      <p>{{ $items->total() }} {{ strtolower(\Illuminate\Support\Str::plural(\Illuminate\Support\Str::singular($title), $items->total())) }} in total</p>
    </div>
  </div>
  <div class="table-scroll">
  <table class="w-full">
    <thead><tr>@foreach($columns as $label => $fn)<th class="th">{{ $label }}</th>@endforeach<th class="th text-right">Actions</th></tr></thead>
    <tbody>
    @forelse($items as $item)
      <tr>
        @foreach($columns as $fn)<td class="td">{{ $fn($item) }}</td>@endforeach
        <td class="td row-actions">
          @foreach($actions($item) as $label => $url)<a href="{{ $url }}" class="btn btn-sm btn-gray">{{ $label }}</a>@endforeach
          <a href="{{ route($route.'.edit', $item->getKey()) }}" class="btn btn-sm">Edit</a>
          <form method="POST" action="{{ route($route.'.destroy', $item->getKey()) }}" class="inline" onsubmit="return confirm('Delete this record?')">
            @csrf @method('DELETE')<button class="btn btn-sm btn-red">Delete</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td class="empty-cell" colspan="{{ count($columns)+1 }}">Nothing here yet. Add your first record to get started.</td></tr>
    @endforelse
    </tbody>
  </table>
  </div>
  @if($items->hasPages())
    <div class="table-panel-footer">
      <span>Showing {{ $items->firstItem() }}–{{ $items->lastItem() }} of {{ $items->total() }}</span>
      {{ $items->withQueryString()->links() }}
    </div>
  @endif
</section>
@endsection
