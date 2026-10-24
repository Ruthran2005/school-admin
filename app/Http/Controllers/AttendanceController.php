<?php
namespace App\Http\Controllers;

use App\Models\{Attendance, SchoolClass, Student};
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $r)
    {
        $classes = SchoolClass::orderBy('name')->orderBy('section')->get();
        $date = $r->date ?: today()->toDateString();
        $class = $r->class ? SchoolClass::find($r->class) : null;
        $students = $class ? Student::with('user')->where('school_class_id', $class->id)->orderBy('roll_no')->get() : collect();
        $existing = $class ? Attendance::where('school_class_id', $class->id)->whereDate('date', $date)->pluck('status', 'student_id') : collect();
        return view('attendance.index', compact('classes', 'class', 'date', 'students', 'existing'));
    }

    public function store(Request $r)
    {
        $r->validate(['class' => 'required|exists:school_classes,id', 'date' => 'required|date|before_or_equal:today',
            'status' => 'required|array', 'status.*' => 'in:present,absent,late']);

        $ids = Student::where('school_class_id', $r->class)->pluck('id')->all();
        foreach ($r->status as $studentId => $status) {
            if (!in_array((int) $studentId, $ids, true)) continue;
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $r->date],
                ['school_class_id' => $r->class, 'status' => $status, 'marked_by' => $r->user()->id]
            );
        }
        return redirect()->route('staff.attendance', ['class' => $r->class, 'date' => $r->date])->with('ok', 'Attendance saved.');
    }
}
