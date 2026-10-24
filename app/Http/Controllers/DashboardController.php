<?php
namespace App\Http\Controllers;

use App\Models\{Attendance, FeeInvoice, Payment, SchoolClass, Student, User};

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $canViewFees = $user->hasModuleAccess('invoices');
        $stats = [
            'students' => $user->hasModuleAccess('students') ? Student::count() : null,
            'teachers' => $user->hasModuleAccess('teachers') ? User::where('role', 'teacher')->count() : null,
            'classes' => $user->hasModuleAccess('classes') ? SchoolClass::count() : null,
            'present' => $user->hasModuleAccess('attendance') ? Attendance::whereDate('date', today())->whereIn('status', ['present', 'late'])->count() : null,
            'absent' => $user->hasModuleAccess('attendance') ? Attendance::whereDate('date', today())->where('status', 'absent')->count() : null,
            'collected' => $canViewFees ? Payment::whereMonth('paid_on', now()->month)->whereYear('paid_on', now()->year)->sum('amount') : null,
            'outstanding' => $canViewFees ? FeeInvoice::sum('amount') - Payment::sum('amount') : null,
        ];
        $recent = $canViewFees ? Payment::with('invoice.student.user')->latest()->take(5)->get() : collect();
        return view('dashboard.admin', compact('stats', 'recent'));
    }
}
