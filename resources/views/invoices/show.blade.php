@extends('layouts.app')
@section('title','Invoice #'.$invoice->id)
@section('content')
<a href="{{ route('admin.invoices.index') }}" class="text-sm text-indigo-600">← All invoices</a>
<div class="grid md:grid-cols-2 gap-4 mt-3">
  <div class="card">
    <h1 class="text-lg font-semibold mb-2">Invoice #{{ $invoice->id }}</h1>
    <p>{{ $invoice->student->user->name }} · {{ $invoice->student->schoolClass->label }} · {{ $invoice->student->admission_no }}</p>
    <p class="text-sm text-gray-500">{{ $invoice->structure->title }} · due {{ $invoice->due_date->format('d M Y') }}</p>
    <div class="mt-3 text-sm">Amount: <b>₹{{ number_format($invoice->amount,2) }}</b> · Paid: <b>₹{{ number_format($invoice->paid(),2) }}</b> · Balance: <b class="text-red-600">₹{{ number_format($invoice->balance(),2) }}</b></div>
    <h2 class="font-semibold mt-4 mb-1">Payments</h2>
    <table class="w-full">
      @forelse($invoice->payments as $p)
        <tr><td class="td">{{ $p->paid_on->format('d M Y') }}</td><td class="td">₹{{ number_format($p->amount,2) }}</td><td class="td">{{ $p->method }}</td>
        <td class="td"><a class="text-indigo-600" href="{{ route('receipts.show',$p) }}" target="_blank">{{ $p->receipt_no }}</a></td></tr>
      @empty<tr><td class="td text-gray-500">No payments yet.</td></tr>@endforelse
    </table>
  </div>
  @if($invoice->balance() > 0)
  <form method="POST" action="{{ route('admin.invoices.pay',$invoice) }}" class="card space-y-3">
    @csrf <h2 class="font-semibold">Record payment</h2>
    <div><label class="text-sm">Amount</label><input type="number" step="0.01" name="amount" value="{{ old('amount',$invoice->balance()) }}" class="input">@error('amount')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror</div>
    <div><label class="text-sm">Method</label><select name="method" class="input">@foreach(['Cash','UPI','Card','Bank transfer','Cheque'] as $m)<option>{{ $m }}</option>@endforeach</select></div>
    <div><label class="text-sm">Date</label><input type="date" name="paid_on" value="{{ today()->toDateString() }}" class="input"></div>
    <button class="btn">Save payment</button>
  </form>
  @endif
</div>
@endsection
