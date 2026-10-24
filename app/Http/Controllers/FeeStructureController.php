<?php
namespace App\Http\Controllers;

use App\Models\{FeeInvoice, FeeStructure, SchoolClass, Student};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeStructureController extends CrudController
{
    protected string $model = FeeStructure::class;
    protected string $route = 'admin.fee-structures';
    protected string $title = 'Fee structures';

    protected function query() { return FeeStructure::with('schoolClass')->withCount('invoices')->latest('id'); }

    protected function columns(): array
    {
        return [
            'Class' => fn ($f) => $f->schoolClass->label, 'Title' => fn ($f) => $f->title,
            'Amount' => fn ($f) => number_format($f->amount, 2), 'Due' => fn ($f) => $f->due_date->format('d M Y'),
            'Invoices' => fn ($f) => $f->invoices_count,
        ];
    }

    protected function fields(): array
    {
        return [
            'school_class_id' => ['Class', 'select', SchoolClass::orderBy('name')->get()->pluck('label', 'id')->all()],
            'title' => ['Fee title (e.g. Term 1 Tuition)', 'text'], 'amount' => ['Amount per student', 'number'], 'due_date' => ['Due date', 'date'],
        ];
    }

    protected function rules(?Model $item = null): array
    {
        return ['school_class_id' => 'required|exists:school_classes,id', 'title' => 'required|string|max:100', 'amount' => 'required|numeric|min:1', 'due_date' => 'required|date'];
    }

    // Creating a structure auto-generates an invoice for every student in that class.
    protected function persist(?Model $item, array $data, Request $r): void
    {
        if ($item) { $item->update($data); return; }
        DB::transaction(function () use ($data) {
            $fs = FeeStructure::create($data);
            Student::where('school_class_id', $fs->school_class_id)->each(fn ($s) => FeeInvoice::create([
                'student_id' => $s->id, 'fee_structure_id' => $fs->id, 'amount' => $fs->amount, 'due_date' => $fs->due_date,
            ]));
        });
    }
}
