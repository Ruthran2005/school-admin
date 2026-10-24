<?php
namespace App\Http\Controllers;

use App\Models\{Exam, Mark, Student, Subject};
use Illuminate\Http\Request;

class MarksController extends Controller
{
    public function index(Request $r)
    {
        $exams = Exam::with('schoolClass')->latest('id')->get();
        $subjects = Subject::orderBy('name')->get();
        $exam = $r->exam ? Exam::find($r->exam) : null;
        $subject = $r->subject ? Subject::find($r->subject) : null;
        $students = collect(); $existing = collect();
        if ($exam && $subject) {
            $students = Student::with('user')->where('school_class_id', $exam->school_class_id)->orderBy('roll_no')->get();
            $existing = Mark::where('exam_id', $exam->id)->where('subject_id', $subject->id)->get()->keyBy('student_id');
        }
        return view('marks.index', compact('exams', 'subjects', 'exam', 'subject', 'students', 'existing'));
    }

    public function store(Request $r)
    {
        $r->validate(['exam_id' => 'required|exists:exams,id', 'subject_id' => 'required|exists:subjects,id',
            'max_marks' => 'required|numeric|min:1|max:1000', 'marks' => 'required|array', 'marks.*' => 'nullable|numeric|min:0|lte:max_marks']);

        $exam = Exam::findOrFail($r->exam_id);
        $ids = Student::where('school_class_id', $exam->school_class_id)->pluck('id')->all();
        foreach ($r->marks as $studentId => $value) {
            if ($value === null || $value === '' || !in_array((int) $studentId, $ids, true)) continue;
            Mark::updateOrCreate(
                ['exam_id' => $exam->id, 'student_id' => $studentId, 'subject_id' => $r->subject_id],
                ['marks_obtained' => $value, 'max_marks' => $r->max_marks]
            );
        }
        return redirect()->route('staff.marks', ['exam' => $exam->id, 'subject' => $r->subject_id])->with('ok', 'Marks saved.');
    }
}
