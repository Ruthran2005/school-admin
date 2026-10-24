@extends('layouts.app')
@section('title','Invoices')
@section('content')
<div class="page-heading">
  <div>
    <p class="page-eyebrow">FINANCE</p>
    <h1 class="page-title">Fee invoices</h1>
    <p class="page-description">Track payments, balances and upcoming due dates.</p>
  </div>
  <form method="GET" class="invoice-filter">
    <label class="sr-only" for="invoice-status">Filter by payment status</label>
    <select id="invoice-status" name="status" class="input" onchange="this.form.submit()"><option value="">All statuses</option>
      @foreach(['unpaid','partial','paid'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>@endforeach
    </select>
  </form>
</div>
<section class="table-panel">
<div class="table-panel-heading"><div><h2>Invoice list</h2><p>{{ $invoices->total() }} invoices in total</p></div></div>
<div class="table-scroll"><table class="w-full">
  <thead><tr><th class="th">Invoice</th><th class="th">Student</th><th class="th">Class</th><th class="th">Fee</th><th class="th">Amount</th><th class="th">Paid</th><th class="th">Due date</th><th class="th">Status</th><th class="th"></th></tr></thead>
  @forelse($invoices as $i)
    <tr><td class="td">{{ $i->id }}</td><td class="td">{{ $i->student->user->name }}</td><td class="td">{{ $i->student->schoolClass->label }}</td>
    <td class="td">{{ $i->structure->title }}</td><td class="td">₹{{ number_format($i->amount,2) }}</td><td class="td">₹{{ number_format($i->payments_sum_amount ?? 0,2) }}</td>
    <td class="td">{{ $i->due_date->format('d M Y') }}</td>
    <td class="td"><span class="px-2 py-0.5 rounded text-xs {{ ['paid'=>'bg-green-100 text-green-800','partial'=>'bg-yellow-100 text-yellow-800','unpaid'=>'bg-red-100 text-red-800'][$i->status] }}">{{ ucfirst($i->status) }}</span></td>
    <td class="td"><a href="{{ route('admin.invoices.show',$i) }}" class="btn btn-sm">Open</a></td></tr>
  @empty<tr><td class="empty-cell" colspan="9">No invoices match this filter.</td></tr>@endforelse
</table></div>
  @if($invoices->hasPages())
    <div class="table-panel-footer"><span>Showing {{ $invoices->firstItem() }}–{{ $invoices->lastItem() }} of {{ $invoices->total() }}</span>{{ $invoices->withQueryString()->links() }}</div>
  @endif
</section>
@endsection
