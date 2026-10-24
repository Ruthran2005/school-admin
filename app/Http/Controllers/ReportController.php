<?php
namespace App\Http\Controllers;

use App\Models\{Exam, Mark, Student};

class ReportController extends Controller
{
    public function card(Student $student, Exam $exam)
    {
        $u = auth()->user();
        abort_unless(
            in_array($u->role, ['admin', 'teacher']) || ($u->role === 'student' && $student->user_id === $u->id),
            403
        );
        $student->load(['user', 'schoolClass']);
        $marks = Mark::with('subject')->where('exam_id', $exam->id)->where('student_id', $student->id)->get();
        $total = $marks->sum('marks_obtained');
        $max = $marks->sum('max_marks');
        $pct = $max ? round($total / $max * 100, 1) : 0;
        $grade = Mark::grade($pct);
        $attendance = $student->attendancePercent();
        return view('report.card', compact('student', 'exam', 'marks', 'total', 'max', 'pct', 'grade', 'attendance'));
    }
}
