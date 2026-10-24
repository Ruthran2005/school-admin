<?php
namespace App\Http\Controllers;

class TeacherController extends UserCrudController
{
    protected string $route = 'admin.teachers';
    protected string $title = 'Teachers';
    protected function role(): string { return 'teacher'; }
}
