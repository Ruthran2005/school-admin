<?php
namespace App\Http\Controllers;

use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Model;

class SchoolClassController extends CrudController
{
    protected string $model = SchoolClass::class;
    protected string $route = 'admin.classes';
    protected string $title = 'Classes';

    protected function query() { return SchoolClass::withCount('students')->orderBy('name')->orderBy('section'); }
    protected function columns(): array
    {
        return ['Class' => fn ($c) => $c->name, 'Section' => fn ($c) => $c->section, 'Students' => fn ($c) => $c->students_count];
    }
    protected function fields(): array { return ['name' => ['Class name (e.g. Class 6)', 'text'], 'section' => ['Section', 'text']]; }
    protected function rules(?Model $item = null): array { return ['name' => 'required|string|max:50', 'section' => 'nullable|string|max:10']; }
}
