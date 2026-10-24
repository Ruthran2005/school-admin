@extends('layouts.app')
@section('title','Receipt '.$payment->receipt_no)
@section('content')
<div class="card max-w-xl mx-auto">
  <div class="text-center border-b pb-3 mb-3"><h1 class="text-xl font-bold">Fee Receipt</h1><p class="text-sm text-gray-500">{{ config('app.name') }}</p></div>
  <table class="w-full text-sm">
    <tr><td class="td">Receipt no</td><td class="td font-medium">{{ $payment->receipt_no }}</td></tr>
    <tr><td class="td">Date</td><td class="td">{{ $payment->paid_on->format('d M Y') }}</td></tr>
    <tr><td class="td">Student</td><td class="td">{{ $student->user->name }} ({{ $student->admission_no }})</td></tr>
    <tr><td class="td">Class</td><td class="td">{{ $student->schoolClass->label }}</td></tr>
    <tr><td class="td">Fee</td><td class="td">{{ $payment->invoice->structure->title }}</td></tr>
    <tr><td class="td">Method</td><td class="td">{{ $payment->method }}</td></tr>
    <tr><td class="td">Amount paid</td><td class="td font-bold">₹{{ number_format($payment->amount,2) }}</td></tr>
    <tr><td class="td">Balance after payment</td><td class="td">₹{{ number_format($payment->invoice->balance(),2) }}</td></tr>
  </table>
  <button onclick="window.print()" class="btn mt-4 noprint">Print</button>
</div>
@endsection
