<?php
namespace App\Http\Controllers;

class AdminUserController extends UserCrudController
{
    protected string $route = 'admin.admins';
    protected string $title = 'Admin users';
    protected function role(): string { return 'admin'; }
}
