<?php
namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;

class SubjectController extends CrudController
{
    protected string $model = Subject::class;
    protected string $route = 'admin.subjects';
    protected string $title = 'Subjects';

    protected function query() { return Subject::orderBy('name'); }
    protected function columns(): array { return ['Subject' => fn ($s) => $s->name, 'Code' => fn ($s) => $s->code]; }
    protected function fields(): array { return ['name' => ['Subject name', 'text'], 'code' => ['Code', 'text']]; }
    protected function rules(?Model $item = null): array
    {
        return ['name' => 'required|string|max:100', 'code' => 'nullable|string|max:20|unique:subjects,code,' . ($item?->id ?? 'NULL')];
    }
}
