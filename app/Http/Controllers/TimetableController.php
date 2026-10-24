<?php
namespace App\Http\Controllers;

use App\Models\{SchoolClass, Subject, Timetable, User};
use Illuminate\Database\Eloquent\Model;

class TimetableController extends CrudController
{
    protected string $model = Timetable::class;
    protected string $route = 'admin.timetable';
    protected string $title = 'Timetable';

    protected function query()
    {
        return Timetable::with(['schoolClass', 'subject', 'teacher'])->orderBy('school_class_id')->orderBy('day')->orderBy('period');
    }

    protected function columns(): array
    {
        return [
            'Class' => fn ($t) => $t->schoolClass->label, 'Day' => fn ($t) => Timetable::DAYS[$t->day], 'Period' => fn ($t) => $t->period,
            'Subject' => fn ($t) => $t->subject->name, 'Teacher' => fn ($t) => $t->teacher?->name,
            'Time' => fn ($t) => substr($t->start_time, 0, 5) . ' - ' . substr($t->end_time, 0, 5),
        ];
    }

    protected function fields(): array
    {
        return [
            'school_class_id' => ['Class', 'select', SchoolClass::orderBy('name')->get()->pluck('label', 'id')->all()],
            'day' => ['Day', 'select', Timetable::DAYS], 'period' => ['Period no.', 'number'],
            'subject_id' => ['Subject', 'select', Subject::orderBy('name')->pluck('name', 'id')->all()],
            'teacher_id' => ['Teacher', 'select', User::where('role', 'teacher')->orderBy('name')->pluck('name', 'id')->all()],
            'start_time' => ['Start time', 'time'], 'end_time' => ['End time', 'time'],
        ];
    }

    protected function rules(?Model $item = null): array
    {
        return [
            'school_class_id' => 'required|exists:school_classes,id', 'day' => 'required|integer|between:1,6',
            'period' => 'required|integer|between:1,12', 'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:users,id', 'start_time' => 'required', 'end_time' => 'required|after:start_time',
        ];
    }
}
