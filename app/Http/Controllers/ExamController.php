<?php
namespace App\Http\Controllers;

use App\Models\{Exam, SchoolClass, Student};
use Illuminate\Database\Eloquent\Model;

class ExamController extends CrudController
{
    protected string $model = Exam::class;
    protected string $route = 'admin.exams';
    protected string $title = 'Exams';

    protected function query() { return Exam::with('schoolClass')->latest('id'); }

    protected function columns(): array
    {
        return ['Exam' => fn ($e) => $e->name, 'Class' => fn ($e) => $e->schoolClass->label, 'Starts' => fn ($e) => $e->start_date?->format('d M Y')];
    }

    protected function rowActions(Model $item): array { return ['Report cards' => route('admin.exams.results', $item->id)]; }

    protected function fields(): array
    {
        return [
            'name' => ['Exam name', 'text'],
            'school_class_id' => ['Class', 'select', SchoolClass::orderBy('name')->get()->pluck('label', 'id')->all()],
            'start_date' => ['Start date', 'date'],
        ];
    }

    protected function rules(?Model $item = null): array
    {
        return ['name' => 'required|string|max:100', 'school_class_id' => 'required|exists:school_classes,id', 'start_date' => 'nullable|date'];
    }

    public function results($id)
    {
        $exam = Exam::with('schoolClass')->findOrFail($id);
        $students = Student::with('user')->where('school_class_id', $exam->school_class_id)->orderBy('roll_no')->get();
        return view('exams.results', compact('exam', 'students'));
    }
}
