<?php
namespace App\Http\Controllers;

use App\Models\{FeeInvoice, Payment};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index(Request $r)
    {
        $invoices = FeeInvoice::with(['student.user', 'student.schoolClass', 'structure'])->withSum('payments', 'amount')
            ->when($r->student, fn ($q, $s) => $q->where('student_id', $s))
            ->when($r->status, fn ($q, $s) => $q->where('status', $s))
            ->latest('id')->paginate(20)->withQueryString();
        return view('invoices.index', compact('invoices'));
    }

    public function show(FeeInvoice $invoice)
    {
        $invoice->load(['student.user', 'student.schoolClass', 'structure', 'payments']);
        return view('invoices.show', compact('invoice'));
    }

    public function pay(Request $r, FeeInvoice $invoice)
    {
        $data = $r->validate([
            'amount' => 'required|numeric|min:1|max:' . $invoice->balance(),
            'method' => 'required|in:Cash,UPI,Card,Bank transfer,Cheque', 'paid_on' => 'required|date',
        ]);
        Payment::create($data + [
            'fee_invoice_id' => $invoice->id, 'received_by' => $r->user()->id,
            'receipt_no' => 'RCT-' . now()->format('ymd') . '-' . Str::upper(Str::random(5)),
        ]);
        $invoice->refreshStatus();
        return back()->with('ok', 'Payment recorded.');
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['invoice.student.user', 'invoice.student.schoolClass', 'invoice.structure', 'receiver']);
        $student = $payment->invoice->student;
        $u = auth()->user();
        abort_unless($u->role === 'admin' || ($u->role === 'student' && $student->user_id === $u->id), 403);
        return view('invoices.receipt', compact('payment', 'student'));
    }
}
