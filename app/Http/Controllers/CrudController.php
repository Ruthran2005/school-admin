<?php
namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

/** Generic admin CRUD. Child controllers only describe columns, fields and rules. */
abstract class CrudController extends Controller
{
    protected string $model;
    protected string $route;
    protected string $title;

    abstract protected function columns(): array;              // label => fn($item)
    abstract protected function fields(): array;               // name => [label, type, options?]
    abstract protected function rules(?Model $item = null): array;

    protected function query() { return $this->model::query()->latest('id'); }
    protected function rowActions(Model $item): array { return []; }   // label => url
    protected function remove(Model $item): void { $item->delete(); }

    protected function persist(?Model $item, array $data, Request $r): void
    {
        $item ? $item->update($data) : $this->model::create($data);
    }

    protected function values(Model $item): array
    {
        $out = [];
        foreach (array_keys($this->fields()) as $k) {
            $v = $item->{$k} ?? null;
            $out[$k] = $v instanceof \DateTimeInterface ? $v->format('Y-m-d') : $v;
        }
        return $out;
    }

    public function index()
    {
        return view('crud.index', [
            'title' => $this->title, 'route' => $this->route, 'columns' => $this->columns(),
            'items' => $this->query()->paginate(15), 'actions' => fn ($i) => $this->rowActions($i),
        ]);
    }

    public function create()
    {
        return $this->form(null);
    }

    public function edit($id)
    {
        return $this->form($this->query()->findOrFail($id));
    }

    private function form(?Model $item)
    {
        return view('crud.form', [
            'title' => ($item ? 'Edit ' : 'Add ') . $this->title, 'route' => $this->route,
            'fields' => $this->fields(), 'values' => $item ? $this->values($item) : [],
            'action' => $item ? route($this->route . '.update', $item->getKey()) : route($this->route . '.store'),
            'editing' => (bool) $item,
        ]);
    }

    public function store(Request $r)
    {
        $this->persist(null, $r->validate($this->rules()), $r);
        return redirect()->route($this->route . '.index')->with('ok', 'Saved successfully.');
    }

    public function update(Request $r, $id)
    {
        $item = $this->query()->findOrFail($id);
        $this->persist($item, $r->validate($this->rules($item)), $r);
        return redirect()->route($this->route . '.index')->with('ok', 'Updated successfully.');
    }

    public function destroy($id)
    {
        try {
            $this->remove($this->query()->findOrFail($id));
        } catch (QueryException $e) {
            return back()->with('err', 'Cannot delete: this record is in use elsewhere.');
        }
        return back()->with('ok', 'Deleted.');
    }
}
