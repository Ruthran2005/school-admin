@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
  <div>
    <p class="mb-1 text-xs font-semibold uppercase tracking-widest text-indigo-500">Overview</p>
    <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Good {{ now()->format('H') < 12 ? 'morning' : (now()->format('H') < 17 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}</h1>
    <p class="mt-1 text-sm text-slate-500">Here’s what’s happening at your school today.</p>
  </div>
  <p class="text-xs font-medium text-slate-500">{{ now()->format('l, d F Y') }}</p>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
  @foreach([
    ['Students', $stats['students'], 'ST', 'bg-indigo-50 text-indigo-600'],
    ['Teachers', $stats['teachers'], 'TE', 'bg-sky-50 text-sky-600'],
    ['Classes', $stats['classes'], 'CL', 'bg-violet-50 text-violet-600'],
    ['Present today', $stats['present'], 'PR', 'bg-emerald-50 text-emerald-600'],
    ['Absent today', $stats['absent'], 'AB', 'bg-rose-50 text-rose-600'],
    ['Collected this month', $stats['collected'] !== null ? '₹'.number_format($stats['collected']) : null, '₹', 'bg-amber-50 text-amber-600'],
    ['Outstanding fees', $stats['outstanding'] !== null ? '₹'.number_format($stats['outstanding']) : null, '₹', 'bg-orange-50 text-orange-600'],
  ] as [$label, $value, $icon, $tone])
    @if($value !== null)
    <div class="card flex items-start justify-between gap-3">
      <div>
        <p class="text-xs font-medium text-slate-500">{{ $label }}</p>
        <p class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $value }}</p>
      </div>
      <span class="grid h-10 w-10 place-items-center rounded-xl text-xs font-bold {{ $tone }}">{{ $icon }}</span>
    </div>
    @endif
  @endforeach
</div>

@if(auth()->user()->hasModuleAccess('invoices'))
<section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
  <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
    <div>
      <h2 class="text-sm font-semibold text-slate-900">Recent payments</h2>
      <p class="mt-1 text-xs text-slate-500">Latest transactions recorded in the system</p>
    </div>
    <a href="{{ route('admin.invoices.index') }}" class="btn btn-quiet btn-sm">View invoices <span aria-hidden="true">→</span></a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead><tr><th class="th">Student</th><th class="th">Amount</th><th class="th">Method</th><th class="th">Date</th><th class="th text-right">Receipt</th></tr></thead>
      <tbody>
      @forelse($recent as $payment)
        <tr>
          <td class="td font-medium text-slate-800">{{ $payment->invoice->student->user->name }}</td>
          <td class="td font-semibold text-slate-800">₹{{ number_format($payment->amount, 2) }}</td>
          <td class="td">{{ $payment->method }}</td>
          <td class="td">{{ $payment->paid_on->format('d M Y') }}</td>
          <td class="td text-right"><a class="font-medium text-indigo-600 hover:text-indigo-800" href="{{ route('receipts.show', $payment) }}">{{ $payment->receipt_no }}</a></td>
        </tr>
      @empty
        <tr><td class="td py-10 text-center text-slate-500" colspan="5">No payments recorded yet.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</section>
@endif
@endsection
