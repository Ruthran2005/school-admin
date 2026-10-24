<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class UserCrudController extends CrudController
{
    protected string $model = User::class;
    abstract protected function role(): string;

    protected function query() { return User::where('role', $this->role())->orderBy('name'); }

    protected function columns(): array
    {
        $columns = ['Name' => fn ($u) => $u->name, 'Email' => fn ($u) => $u->email, 'Phone' => fn ($u) => $u->phone];

        if ($this->role() === 'admin') {
            $columns['Module access'] = fn ($u) => $u->admin_permissions === null
                ? 'All modules'
                : collect($u->admin_permissions)->map(fn ($module) => User::ADMIN_MODULES[$module] ?? $module)->implode(', ');
        }

        return $columns;
    }

    protected function fields(): array
    {
        $fields = [
            'name' => ['Full name', 'text'], 'email' => ['Email (login)', 'email'],
            'phone' => ['Phone', 'text'], 'password' => ['Password', 'password'],
        ];

        if ($this->role() === 'admin') {
            $fields['admin_permissions'] = ['Module access', 'checkboxes', User::ADMIN_MODULES];
        }

        return $fields;
    }

    protected function rules(?Model $item = null): array
    {
        $rules = [
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . ($item?->id ?? 'NULL'),
            'phone' => 'nullable|string|max:20',
            'password' => ($item ? 'nullable' : 'required') . '|string|min:8',
        ];

        if ($this->role() === 'admin') {
            $rules['admin_permissions'] = 'required|array|min:1';
            $rules['admin_permissions.*'] = 'required|string|in:' . implode(',', array_keys(User::ADMIN_MODULES));
        }

        return $rules;
    }

    protected function persist(?Model $item, array $data, Request $r): void
    {
        $data['role'] = $this->role();
        if (empty($data['password'])) unset($data['password']);
        $item ? $item->update($data) : User::create($data);
    }

    protected function remove(Model $item): void
    {
        abort_if($item->id === auth()->id(), 403, 'You cannot delete your own account.');
        abort_if($item->role === 'admin' && User::where('role', 'admin')->count() <= 1, 403, 'Cannot delete the last admin.');
        $item->delete();
    }
}
