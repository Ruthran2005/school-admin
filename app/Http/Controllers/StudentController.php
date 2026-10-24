<?php
namespace App\Http\Controllers;

use App\Models\{FeeInvoice, FeeStructure, SchoolClass, Student, User};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends CrudController
{
    protected string $model = Student::class;
    protected string $route = 'admin.students';
    protected string $title = 'Students';

    protected function query()
    {
        return Student::with(['user', 'schoolClass'])
            ->when(request('class'), fn ($q, $c) => $q->where('school_class_id', $c))
            ->orderBy('school_class_id')->orderBy('roll_no');
    }

    protected function columns(): array
    {
        return [
            'Adm. No' => fn ($s) => $s->admission_no, 'Name' => fn ($s) => $s->user->name,
            'Class' => fn ($s) => $s->schoolClass->label, 'Roll' => fn ($s) => $s->roll_no,
        ];
    }

    protected function rowActions(Model $item): array
    {
        return ['Invoices' => route('admin.invoices.index', ['student' => $item->id])];
    }

    protected function fields(): array
    {
        return [
            'name' => ['Student name', 'text'], 'email' => ['Student login email', 'email'],
            'password' => ['Student password', 'password'], 'phone' => ['Phone', 'text'],
            'school_class_id' => ['Class', 'select', SchoolClass::orderBy('name')->get()->pluck('label', 'id')->all()],
            'admission_no' => ['Admission No', 'text'], 'roll_no' => ['Roll No', 'number'],
            'dob' => ['Date of birth', 'date'], 'gender' => ['Gender', 'select', ['Male' => 'Male', 'Female' => 'Female', 'Other' => 'Other']],
            'address' => ['Address', 'text'],
        ];
    }

    protected function rules(?Model $item = null): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . ($item?->user_id ?? 'NULL'),
            'password' => ($item ? 'nullable' : 'required') . '|string|min:8',
            'phone' => 'nullable|string|max:20',
            'school_class_id' => 'required|exists:school_classes,id',
            'admission_no' => 'required|string|max:30|unique:students,admission_no,' . ($item?->id ?? 'NULL'),
            'roll_no' => 'nullable|integer|min:1', 'dob' => 'nullable|date',
            'gender' => 'nullable|in:Male,Female,Other', 'address' => 'nullable|string|max:255',
        ];
    }

    protected function values(Model $item): array
    {
        return array_merge(parent::values($item), [
            'name' => $item->user->name, 'email' => $item->user->email, 'phone' => $item->user->phone,
        ]);
    }

    protected function persist(?Model $item, array $data, Request $r): void
    {
        DB::transaction(function () use ($item, $data) {
            $user = ['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'] ?? null, 'role' => 'student'];
            if (!empty($data['password'])) $user['password'] = $data['password'];

            $profile = array_intersect_key($data, array_flip(['school_class_id', 'admission_no', 'roll_no', 'dob', 'gender', 'address']));

            if ($item) {
                $item->user->update($user);
                $item->update($profile);
                return;
            }

            $student = Student::create($profile + ['user_id' => User::create($user)->id]);
            // Auto-generate invoices for fee structures already defined for this class
            FeeStructure::where('school_class_id', $student->school_class_id)->each(fn ($fs) => FeeInvoice::create([
                'student_id' => $student->id, 'fee_structure_id' => $fs->id, 'amount' => $fs->amount, 'due_date' => $fs->due_date,
            ]));
        });
    }

    protected function remove(Model $item): void
    {
        DB::transaction(function () use ($item) {
            $user = $item->user;
            $item->delete();
            $user->delete();
        });
    }
}
