<?php
namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'school:create-admin {--name=} {--email=} {--password=}';
    protected $description = 'Create a new admin user for the School ERP';

    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Name');
        $email = $this->option('email') ?: $this->ask('Email');
        $password = $this->option('password') ?: $this->secret('Password (min 8 chars)');

        if (strlen((string) $password) < 8) { $this->error('Password must be at least 8 characters.'); return self::FAILURE; }
        if (User::where('email', $email)->exists()) { $this->error('That email already exists.'); return self::FAILURE; }

        User::create(['name' => $name, 'email' => $email, 'password' => $password, 'role' => 'admin']);
        $this->info("Admin created: {$email}");
        return self::SUCCESS;
    }
}
